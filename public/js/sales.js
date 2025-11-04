$(document).ready(function () {
    $.noConflict();

    const token = $('meta[name="csrf-token"]').attr("content");
    const modalDetail = $("#modal-detail");

    const table = $("#crud")
        .DataTable({
            ajax: "/sales",
            serverSide: true,
            processing: true,
            aaSorting: [[0, "desc"]],
            language: { url: "/json/datatable-ES.json" },
            dom: "Bftirp",
            columns: [
                { data: "id", name: "id" },
                { data: "created_at", name: "created_at" },
                { data: "buyer_name", name: "buyer_name" },
                { data: "grand_total", name: "grand_total" },
                {
                    data: "payment_status",
                    name: "payment_status",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "status",
                    name: "status",
                    orderable: false,
                    searchable: false,
                },
                { data: "action", orderable: false, searchable: false },
            ],
        })
        .on("processing.dt", function (e, settings, processing) {
            if (processing) {
                Swal.fire({
                    title: "Favor Esperar",
                    timer: 1000000,
                    timerProgressBar: true,
                    showCloseButton: true,
                    didOpen: function () {
                        Swal.showLoading();
                    },
                });
            } else {
                Swal.close();
            }
        });

    $.ajaxSetup({ headers: { "X-CSRF-TOKEN": token } });

    // Ver detalle
    $(document).on("click", ".btn-detail", function () {
        const id = $(this).data("id");
        modalDetail.modal("show");
        $("#detail-body").html("<p>Cargando información...</p>");

        $.get(`/sales/${id}`, function (res) {
            let o = res.order;
            let html = `
            <h5 class="mb-3"><b>Datos del Pedido</b></h5>
            <div class="row small">
                <div class="col-md-6 mb-1">
                <b>Folio:</b> ${o.id}
                </div>
                <div class="col-md-6 mb-1">
                <b>Fecha:</b> ${o.created_at}
                </div>
                <div class="col-md-6 mb-1">
                <b>Cliente:</b> ${o.buyer_name}
                </div>
                <div class="col-md-6 mb-1">
                <b>Email:</b> ${o.buyer_email || "-"}
                </div>
                <div class="col-md-6 mb-1">
                <b>Teléfono:</b> ${o.buyer_phone || "-"}
                </div>
                <div class="col-md-6 mb-1">
                <b>Documento:</b> ${o.doc_type || "-"}
                </div>
                <div class="col-md-12 mb-1">
                <b>Notas:</b> ${o.notes || "-"}
                </div>
                <div class="col-md-6 mb-1">
                <b>Moneda:</b> ${o.currency}
                </div>
                <div class="col-md-6 mb-1">
                <b>Subtotal Neto:</b> $${o.subtotal_net}
                </div>
                <div class="col-md-6 mb-1">
                <b>IVA:</b> $${o.tax_total}
                </div>
                <div class="col-md-6 mb-1">
                <b>Total:</b> <span class="text-success fw-bold">$${
                    o.grand_total
                }</span>
                </div>
            </div>

            <hr class="my-3">
            <h5 class="mb-3"><b>Productos</b></h5>
            `;

            res.items.forEach((it) => {
                html += `<div class="card card-secondary border-1 mb-3">
                    <div class="card-body card-body-gray py-3 px-4">
                    <div class="row g-0 align-items-stretch">
                    <div class="col-md-4 d-flex flex-column align-items-center justify-content-center rounded-start p-2">

                        ${
                            it.attachments && it.attachments.length
                                ? it.attachments
                                      .map((a) => {
                                          const isImg =
                                              a.mime &&
                                              a.mime.startsWith("image/");
                                          const preview = isImg
                                              ? `<img src="${a.url}" class="img-fluid rounded border mb-2" style="max-height:160px;object-fit:contain;">`
                                              : `<div class="text-secondary"><i class="fa fa-paperclip me-2"></i>${
                                                    a.name || "Archivo"
                                                }</div>`;
                                          return `
                                    <div class="w-100 text-center">
                                    ${preview}
                                    <a href="${a.url}" target="_blank" class="btn btn-outline-primary btn-sm mt-1">
                                        <i class="fa fa-download me-1"></i> Ver / Descargar
                                    </a>
                                    </div>`;
                                      })
                                      .join("")
                                : ""
                        }
                    </div>

                    <!-- Columna derecha: descripción -->
                    <div class="col-md-8">
                        
                        <h6 class="card-title mb-1"><b>${
                            it.product_name
                        }</b></h6>
                        <p class="card-text small text-muted mb-2">
                            <ul class="small text-muted mb-0">
                                <li>Cantidad: <b>${it.qty_real}</b></li>
                                <li>Precio unitario: <b>$${
                                    it.unit_price
                                }</b></li>
                                <li>Total: <b>$${it.line_total}</b></li>
                            </ul>
                        </p>

                        ${
                            it.options && it.options.length
                                ? `
                            <div class="mb-2">
                                <b>Opciones seleccionadas:</b>
                                <ul class="small text-muted mb-0">
                                ${it.options
                                    .map(
                                        (op) =>
                                            `<li>${op.group}: ${op.value}</li>`
                                    )
                                    .join("")}
                                </ul>
                            </div>`
                                : ""
                        }

                        ${
                            it.notes
                                ? `<div class="border-top pt-2 mt-2 small text-muted"><b>Notas:</b> ${it.notes}</div>`
                                : ""
                        }
                        </div>
                    </div>
                    </div>
                </div>`;
            });

            if (res.address) {
                let a = res.address;
                html += `<hr><h5><b>Dirección de envío</b></h5>
            <p>${a.line1 ?? ""}<br>
            ${a.commune?.name ?? ""}, ${a.region?.name ?? ""}, ${
                    a.country?.name ?? ""
                }<br>
            Ref: ${a.reference ?? "-"}</p>`;
            }

            if (res.documents.length > 0) {
                html += `<hr><h5><b>Documentos</b></h5>`;
                res.documents.forEach((d) => {
                    html += `<p>${d.type} (${d.status}) - ${d.issued_at || "-"}
                ${
                    d.pdf
                        ? `<a href="${d.pdf}" target="_blank" class="btn btn-sm btn-outline-primary ms-2"><i class="fa fa-file-pdf"></i> Ver PDF</a>`
                        : ""
                }
            </p>`;
                });
            }

            if (res.logs.length > 0) {
                html += `<hr><h5><b>Historial</b></h5><ul>`;
                res.logs.forEach((l) => {
                    html += `<li><b>${l.date}</b> - ${l.user || "Sistema"}: ${
                        l.from || "(inicio)"
                    } → ${l.to} (${l.msg})</li>`;
                });
                html += `</ul>`;
            }

            $("#detail-body").html(html);
        });
    });

    // Cambiar estado o estado de pago
    // Cambiar estado o estado de pago
    $(document).on("click", ".btn-status", function () {
        const id = $(this).data("id");

        // Traer info actual antes de abrir el modal
        $.get(`/sales/${id}/status`, function (res) {
            const currentStatus = res.status;
            const currentPayment = res.payment_status;

            const formHtml = `
            <div class="text-start">
                <label class="form-label fw-bold mb-1">Estado de la Venta</label>
                <select id="swal-status" class="form-select mb-3">
                    <option value="">-- Sin cambio --</option>
                    <option value="pending_payment" ${
                        currentStatus === "pending_payment" ? "selected" : ""
                    }>Pendiente de Pago</option>
                    <option value="processing" ${
                        currentStatus === "processing" ? "selected" : ""
                    }>Procesando</option>
                    <option value="completed" ${
                        currentStatus === "completed" ? "selected" : ""
                    }>Completada</option>
                    <option value="cancelled" ${
                        currentStatus === "cancelled" ? "selected" : ""
                    }>Cancelada</option>
                </select>
                <br>
                <label class="form-label fw-bold mb-1">Estado del Pago</label>
                <select id="swal-payment" class="form-select">
                    <option value="">-- Sin cambio --</option>
                    <option value="unpaid" ${
                        currentPayment === "unpaid" ? "selected" : ""
                    }>No pagado</option>
                    <option value="pending" ${
                        currentPayment === "pending" ? "selected" : ""
                    }>Pendiente de Confirmación</option>
                    <option value="paid" ${
                        currentPayment === "paid" ? "selected" : ""
                    }>Pagado</option>
                    <option value="refunded" ${
                        currentPayment === "refunded" ? "selected" : ""
                    }>Reembolsado</option>
                </select>
            </div>
        `;

            Swal.fire({
                title: "Actualizar Estados",
                html: formHtml,
                showCancelButton: true,
                confirmButtonText: "Guardar cambios",
                cancelButtonText: "Cancelar",
                focusConfirm: false,
                preConfirm: () => {
                    const status = $("#swal-status").val();
                    const payment_status = $("#swal-payment").val();
                    if (
                        status === currentStatus &&
                        payment_status === currentPayment
                    ) {
                        Swal.showValidationMessage(
                            "Debes realizar algún cambio antes de guardar"
                        );
                    }
                    return { status, payment_status };
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(
                        `/sales/${id}/status`,
                        {
                            _token: token,
                            status: result.value.status,
                            payment_status: result.value.payment_status,
                            old_status: currentStatus,
                            old_payment_status: currentPayment,
                        },
                        function (res) {
                            if (res.status == 200) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Estados actualizados correctamente",
                                    timer: 1500,
                                    showConfirmButton: false,
                                });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title:
                                        res.message || "No se pudo actualizar",
                                });
                            }
                        }
                    ).fail(() =>
                        Swal.fire({
                            icon: "error",
                            title: "Error al actualizar estado",
                        })
                    );
                }
            });
        });
    });
});
