// product-config.js
// Requiere Bootstrap 5; Select2 es opcional
(function () {
    const modalEl = document.getElementById("productConfigModal");
    if (!modalEl) return;

    const ROUTES = window.STORE_ROUTES || {};
    const pcModal = new bootstrap.Modal(modalEl);

    const carInner = modalEl.querySelector("#pc_carousel .carousel-inner");
    const titleEl = modalEl.querySelector("#pc_title");
    const subEl = modalEl.querySelector("#pc_subtitle");
    const fileIn = modalEl.querySelector("#pc_file");
    const fileLbl = modalEl.querySelector("#pc_filename");
    const totalEl = modalEl.querySelector("#pc_total");
    const dynBox = modalEl.querySelector("#pc_dynamic"); // contenedor dinámico

    const qtyRow = modalEl.querySelector("#div_quantity"); // fila cantidad libre
    const qtyInput = modalEl.querySelector("#pc_qty"); // input cantidad libre
    const notesEl = modalEl.querySelector("#pc_notes");

    function csrf() {
        return window.CSRF_TOKEN || "";
    }

    function fillCarousel(urls = []) {
        carInner.innerHTML = "";
        if (!urls.length) urls = [modalEl.dataset.noimg || "/img/no-image.jpg"];
        urls.forEach((u, idx) => {
            const item = document.createElement("div");
            item.className = "carousel-item" + (idx === 0 ? " active" : "");
            item.innerHTML = `<img src="${u}" class="d-block w-100 object-fit-cover" style="max-height:520px">`;
            carInner.appendChild(item);
        });
    }

    function resetModal() {
        dynBox.innerHTML = "";
        if (qtyInput) qtyInput.value = 1;
        if (notesEl) notesEl.value = "";
        if (fileIn) fileIn.value = "";
        if (fileLbl) fileLbl.textContent = "No se eligió ningún archivo";
        if (totalEl) totalEl.value = "0.000";
        modalEl.removeAttribute("data-pid");
    }

    // Construye 1 grupo (label + select)
    function buildSelectGroup(group) {
        const wrap = document.createElement("div");
        wrap.className = "d-flex align-items-center gap-3 mb-3";
        wrap.innerHTML = `
      <div class="fw-semibold" style="min-width:140px">${group.name}</div>
      <div class="flex-grow-1">
        <select class="form-select" ${
            group.multi ? "multiple" : ""
        } data-code="${group.code}">
          ${
              group.multi
                  ? ""
                  : `<option value="">${
                        group.placeholder || "Seleccione…"
                    }</option>`
          }
          ${(group.options || [])
              .map((o) => `<option value="${o.id}">${o.text}</option>`)
              .join("")}
        </select>
      </div>
    `;

        const select = wrap.querySelector("select");

        // Preselección
        if (Array.isArray(group.selected) && group.selected.length) {
            if (group.multi) {
                group.selected.forEach((v) => {
                    const opt = [...select.options].find((op) => op.value == v);
                    if (opt) opt.selected = true;
                });
            } else {
                const first = group.selected[0];
                const opt = [...select.options].find((op) => op.value == first);
                if (opt) opt.selected = true;
            }
        }

        // Select2 opcional
        if (window.jQuery && jQuery.fn.select2) {
            jQuery(select).select2({
                width: "100%",
                dropdownParent: jQuery(modalEl),
                placeholder: group.placeholder || "Seleccione…",
            });
        }
        return wrap;
    }

    // Recolecta { code: [ids] }
    function collectOptions() {
        const opts = {};
        dynBox.querySelectorAll("select[data-code]").forEach((sel) => {
            const code = sel.getAttribute("data-code");
            const values = Array.from(sel.selectedOptions)
                .map((o) => o.value)
                .filter(Boolean);
            if (code) opts[code] = values;
        });
        return opts;
    }

    async function fetchPrice(pid, opts) {
        const url = (
            ROUTES.productPrice || "/store/products/:pid/price"
        ).replace(":pid", pid);
        const params = new URLSearchParams();

        // NO mandamos qty al endpoint: siempre recibimos precio unitario
        Object.entries(opts).forEach(([code, ids]) => {
            const id = Array.isArray(ids) ? ids[0] : ids; // uno por tipo
            if (id !== undefined && id !== null && id !== "") {
                params.append(`${code}_id`, id);
            }
        });

        const res = await fetch(`${url}?${params.toString()}`, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const json = await res.json();

        // Tomar precio unitario del response
        const unit =
            (json.price != null ? Number(json.price) : null) ??
            (json.unit_price != null ? Number(json.unit_price) : null) ??
            (json.data && json.data.price != null
                ? Number(json.data.price)
                : null) ??
            0;

        const usesQtyGroup = modalEl.dataset.usesQtyGroup === "1";

        // qty: si usa grupo, no multiplicamos; si NO usa, multiplicamos por pc_qty
        let qty = 1;
        if (!usesQtyGroup && qtyInput) {
            qty = parseInt(qtyInput.value, 10) || 1;
        }

        const total = unit * qty;

        if (json.status === 200 && total > 0) {
            totalEl.value = Math.round(total).toLocaleString("es-CL");
            totalEl.dataset.price = String(total);
            document.getElementById("pc_add").disabled = false;
        } else {
            totalEl.value = "0";
            delete totalEl.dataset.price;
            document.getElementById("pc_add").disabled = true;
        }
    }

    // Cuando cambie cualquier select, intenta calcular el precio
    dynBox.addEventListener("change", async () => {
        const pid = modalEl.getAttribute("data-pid");
        const opts = collectOptions();

        // Solo consultar si todos los grupos requeridos tienen valor
        const allFilled = Object.values(opts).every((arr) => arr && arr.length);
        if (allFilled) {
            await fetchPrice(pid, opts);
        } else {
            totalEl.value = "0";
            delete totalEl.dataset.price;
            document.getElementById("pc_add").disabled = true;
        }
    });

    qtyInput?.addEventListener("input", async () => {
        if (modalEl.dataset.usesQtyGroup === "1") return; // si usa grupo, no multiplicamos aquí
        const pid = modalEl.getAttribute("data-pid");
        const opts = collectOptions();

        const allFilled = Object.values(opts).every((arr) => arr && arr.length);
        if (allFilled) {
            await fetchPrice(pid, opts);
        } else {
            totalEl.value = "0";
            delete totalEl.dataset.price;
            document.getElementById("pc_add").disabled = true;
        }
    });

    function hasQuantityGroup(json) {
        return (json.groups || []).some((g) => g.code === "quantity");
    }

    if (fileIn && fileLbl) {
        fileIn.addEventListener("change", () => {
            fileLbl.textContent =
                fileIn.files?.[0]?.name || "No se eligió ningún archivo";
        });
    }

    // Abrir modal desde navbar/cards
    document.addEventListener("click", async (e) => {
        const a = e.target.closest(".js-open-product");
        if (!a) return;
        e.preventDefault();

        const pid = a.dataset.pid;
        const name = a.dataset.name || "Producto";
        const sub = a.dataset.sub || "";

        resetModal();
        modalEl.setAttribute("data-pid", pid);
        if (titleEl) titleEl.textContent = name;
        if (subEl) subEl.textContent = sub;

        try {
            const url = (
                ROUTES.productConfig || "/store/products/:pid/config"
            ).replace(":pid", pid);
            const res = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            const json = await res.json();

            const usesQtyGroup = hasQuantityGroup(json);
            modalEl.dataset.usesQtyGroup = usesQtyGroup ? "1" : "0";
            if (qtyRow) qtyRow.classList.toggle("d-none", usesQtyGroup);

            if (qtyRow)
                qtyRow.classList.toggle("d-none", hasQuantityGroup(json));
            fillCarousel(json.photos || []);
            (json.groups || []).forEach((g) =>
                dynBox.appendChild(buildSelectGroup(g))
            );

            pcModal.show();
        } catch (err) {
            console.error(err);
            alert("No se pudo cargar la configuración del producto.");
        }
    });

    // Agregar al carrito
    modalEl.querySelector("#pc_add")?.addEventListener("click", async () => {
        const btn = modalEl.querySelector("#pc_add");
        if (!btn) return;
        btn.disabled = true;

        try {
            const pid = modalEl.getAttribute("data-pid");
            const opts = collectOptions();

            // qty: si existe grupo 'quantity', usa ese; si no, input libre
            let qty = 1;
            if (opts.quantity && opts.quantity.length) {
                qty = parseInt(opts.quantity[0], 10) || 1;
                delete opts.quantity;
            } else if (qtyInput) {
                qty = parseInt(qtyInput.value, 10) || 1;
            }

            const notes = notesEl ? (notesEl.value || "").trim() : "";

            const fd = new FormData();
            fd.append("product_id", pid);
            fd.append("qty", String(qty));
            if (notes) fd.append("notes", notes);

            // options[code][] = id
            Object.entries(opts).forEach(([code, ids]) => {
                (Array.isArray(ids) ? ids : [ids]).forEach((val) => {
                    if (val !== undefined && val !== null && val !== "") {
                        fd.append(`options[${code}][]`, val);
                    }
                });
            });

            // archivo si el usuario eligió algo
            if (fileIn && fileIn.files && fileIn.files[0]) {
                const f = fileIn.files[0];
                const ok =
                    f.type.startsWith("image/") || f.type === "application/pdf";
                if (!ok) {
                    alert("Sólo imágenes o PDF.");
                    btn.disabled = false;
                    return;
                }
                fd.append("attachment", f);
            }

            const res = await fetch(ROUTES.cartAdd || "/store/cart/add", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": window.CSRF_TOKEN || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: fd,
            });
            const json = await res.json();
            if (json.status !== 200)
                throw new Error(json.message || "Error al agregar.");

            // Toast OK
            const toastEl = document.createElement("div");
            toastEl.className =
                "toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3";
            toastEl.innerHTML =
                '<div class="d-flex"><div class="toast-body">Agregado al carrito.</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
            document.body.appendChild(toastEl);
            bootstrap.Toast.getOrCreateInstance(toastEl).show();

            setCartBadge(
                (json.summary &&
                    (json.summary.qty_total ?? json.summary.items_count)) ||
                    0
            );
            if (window.refreshMiniCart) await window.refreshMiniCart();

            // cerrar y reset
            modalEl.querySelector("#pc_file").value = "";
            modalEl.querySelector("#pc_filename").textContent =
                "No se eligió ningún archivo";
            pcModal.hide();
        } catch (err) {
            console.error(err);
            alert("No se pudo agregar al carrito.");
        } finally {
            btn.disabled = false;
        }
    });
})();

// =========================
// Offcanvas mini-carrito
// =========================
function setCartBadge(n) {
    const el = document.getElementById("cartCount");
    if (!el) return;
    n = parseInt(n, 10) || 0;
    if (n > 0) {
        el.textContent = n;
        el.style.display = "inline-block";
    } else {
        el.style.display = "none";
    }
}

(function () {
    const offcanvasEl = document.getElementById("cartOffcanvas");
    if (!offcanvasEl) return;

    const ROUTES = window.STORE_ROUTES || {};
    const listEl = document.getElementById("cartItems");
    const subEl = document.getElementById("cartSubtotal");
    const taxEl = document.getElementById("cartTax");
    const totEl = document.getElementById("cartTotal");

    function fmt(n) {
        n = Math.round(n || 0);
        return n.toLocaleString("es-CL");
    }

    function rowTemplate(item) {
        // Construir “Cantidad: 42 · Tamaño: 9×5 cm (Tarjeta) · Papel: Couché 300 g …”
        let optsText = "";
        if (
            Array.isArray(item.options_display) &&
            item.options_display.length
        ) {
            optsText = item.options_display
                .map((o) => `${o.group}: ${o.value}`)
                .join(" · ");
        }

        // Si además quieres anteponer siempre “Cantidad: X”:
        const qtyText = `Cantidad: ${item.qty}`;
        const detail = [qtyText, optsText].filter(Boolean).join(" · ");

        return `
            <div class="d-flex gap-3 align-items-start border-bottom pb-3">
            <img src="${
                item.thumb
            }" class="rounded object-fit-cover" width="72" height="72" alt="">
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                <strong>${item.product.name}</strong>
                <button class="btn btn-sm btn-outline-danger js-cart-remove" data-row="${
                    item.row_id
                }">
                    <i class="bi bi-x"></i>
                </button>
                </div>
                <div class="text-secondary small">${detail}</div>
                <div class="fw-semibold mt-1">$ ${fmt(item.line_net)}</div>
            </div>
            </div>
        `;
    }

    function emptyTemplate() {
        return `
      <div class="text-center text-secondary py-5">
        <i class="bi bi-bag fs-1 d-block mb-2"></i>
        Tu carrito está vacío
      </div>`;
    }

    function bindRemoveButtons() {
        listEl.querySelectorAll(".js-cart-remove").forEach((btn) => {
            btn.addEventListener("click", async (ev) => {
                ev.preventDefault();
                const row = btn.dataset.row;
                try {
                    const url = (
                        ROUTES.cartRemove || "/store/cart/remove/:rowId"
                    ).replace(":rowId", row);
                    const res = await fetch(url, {
                        method: "DELETE",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": window.CSRF_TOKEN || "",
                        },
                    });
                    const json = await res.json();
                    if (json.status === 200) renderSummary(json.summary);
                } catch (e) {
                    console.error(e);
                }
            });
        });
    }

    function renderSummary(sum) {
        listEl.innerHTML =
            !sum.items || !sum.items.length
                ? emptyTemplate()
                : sum.items.map(rowTemplate).join("");

        if (subEl) subEl.textContent = "$ " + fmt(sum.totals.subtotal);
        if (taxEl) taxEl.textContent = "$ " + fmt(sum.totals.tax);
        if (totEl) totEl.textContent = "$ " + fmt(sum.totals.total);
        setCartBadge(sum.items_count);
        bindRemoveButtons();
    }

    document
        .getElementById("cartCheckout")
        ?.addEventListener("click", async (e) => {
            e.preventDefault();
            const btn = e.currentTarget;
            btn.disabled = true;

            try {
                const res = await fetch(
                    ROUTES.cartSummary || "/store/cart/summary",
                    {
                        headers: { "X-Requested-With": "XMLHttpRequest" },
                    }
                );
                const json = await res.json();

                const count =
                    json?.summary?.items_count ??
                    null ??
                    (Array.isArray(json?.summary?.items)
                        ? json.summary.items.length
                        : 0);

                if (json.status === 200 && count > 0) {
                    location.href = ROUTES.cartCheckout || "/store/checkout";
                } else {
                    // vacío
                    Swal.fire({
                        icon: "info",
                        title: "Tu carrito está vacío",
                        text: "Agrega productos para continuar al pago.",
                    });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: "error",
                    title: "No pudimos revisar tu carrito",
                    text: "Intenta nuevamente.",
                });
            } finally {
                btn.disabled = false;
            }
        });

    async function fetchSummary() {
        const res = await fetch(ROUTES.cartSummary || "/store/cart/summary", {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const json = await res.json();
        if (json.status === 200) renderSummary(json.summary);
    }

    // Hazlo disponible globalmente para llamarlo tras "Agregar al carrito"
    window.refreshMiniCart = fetchSummary;

    // Actualizar cada vez que se abre el offcanvas
    offcanvasEl.addEventListener("show.bs.offcanvas", fetchSummary);

    fetchSummary().catch(() => {
        /* ignore */
    });

    // dentro de tu script del mini-cart
    document
        .getElementById("cartClear")
        ?.addEventListener("click", async (e) => {
            e.preventDefault();
            try {
                const res = await fetch(ROUTES.cartClear, {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": window.CSRF_TOKEN,
                    },
                });
                const json = await res.json();
                if (json.status === 200) {
                    renderSummary(json.summary);
                }
            } catch (err) {
                console.error(err);
            }
        });
})();
