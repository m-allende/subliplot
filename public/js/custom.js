$(".number").inputmask({
    mask: "9{1,30}",
});

(function () {
    // Defaults comunes (puedes tocar dom, lengthMenu, etc.)
    const COMMON = {
        serverSide: true,
        processing: true,
        aaSorting: [[0, "asc"]],
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Todos"],
        ],
        language: { url: "/json/datatable-ES.json" },
        dom:
            // fila superior: length + botones a la derecha
            "<'row mb-2'<'col-sm-12 text-right'fB>>" +
            // tabla
            "t" +
            // fila inferior: info + paginación
            "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            {
                extend: "excel",
                text: '<i class="fa fa-file-excel"></i> Exportar',
                titleAttr: "Exportar a Excel",
                className: "btn btn-sm",
                exportOptions: {
                    // exporta solo columnas visibles (lo que ves en pantalla)
                    columns: ":visible",
                },
            },
            {
                text: '<i class="fa fa-sync-alt"></i> Recargar',
                className: "btn btn-sm",
                action: function (e, dt) {
                    dt.ajax.reload(null, false);
                },
            },
            {
                extend: "pageLength",
                className: "btn btn-sm bg-gradient-dark text-white",
            },
        ],
        // Hook para cambiar búsqueda a "Enter"
        initComplete: function () {
            const api = this.api();
            const $wrapper = $(api.table().container());
            const $search = $wrapper.find('input[type="search"]');

            // quitar eventos default
            $search.off(".DT");

            // buscar SOLO al presionar Enter
            $search.on("keypress.DT", function (e) {
                if (e.which === 13) {
                    // Enter
                    api.search(this.value).draw();
                }
            });
        },
    };

    /**
     * Inicializador único para todas tus tablas CRUD.
     * @param {string|Element|jQuery} selector  - ej: '#crud'
     * @param {object} options                  - { ajax, columns, ... }
     * @returns DataTable instance
     */
    window.initCrudTable = function (selector, options) {
        const finalOptions = $.extend(true, {}, COMMON, options || {});
        const dt = $(selector).DataTable(finalOptions);

        // Exponer un método de recarga vía DOM si lo necesitas
        $(selector).data("reload", () => dt.ajax.reload(null, false));

        return dt;
    };

    // Si quieres aplicar defaults globales sin usar initCrudTable:
    // $.extend(true, $.fn.dataTable.defaults, COMMON);
})();

// dt-processing.js
(function () {
    let swalOpen = false;
    let closeTimer = null;

    function openSwal() {
        if (swalOpen) return;
        swalOpen = true;
        Swal.fire({
            title: "Favor esperar",
            html: '<small class="text-muted">Cargando datos…</small>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading(),
        });
        // “cinturón de seguridad”: si algo falla, cerramos a los 25s
        clearTimeout(closeTimer);
        closeTimer = setTimeout(forceClose, 25000);
    }

    function closeSwal() {
        if (swalOpen && Swal.isVisible()) Swal.close();
        swalOpen = false;
        clearTimeout(closeTimer);
    }

    function forceClose() {
        if (swalOpen) {
            closeSwal();
            console.warn("[DT] Cerrando loader por timeout de seguridad.");
        }
    }

    // 1) Evento estándar de DataTables
    $(document).on("processing.dt", function (_e, _settings, processing) {
        processing ? openSwal() : closeSwal();
    });

    // 2) Asegurar cierre cuando llega la respuesta o hay error
    $(document).on("xhr.dt draw.dt error.dt", function () {
        closeSwal();
    });

    // 3) Si un request falla a nivel jQuery AJAX, cerramos también
    $(document).ajaxError(function (_ev, _jqxhr, _opts, _err) {
        closeSwal();
    });

    // 4) Si el usuario cambia de pestaña/ruta, no dejar el modal colgado
    window.addEventListener("beforeunload", closeSwal);
})();
