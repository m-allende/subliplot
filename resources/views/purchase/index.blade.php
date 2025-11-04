@extends('adminlte::page')

@section('title', 'Compras')

@section('content_header')
    &nbsp;
@endsection

@section('content')
    <div class="row justify-content-md-center">
        <div class="col">
            <div class="card card-secondary">
                <div class="card-header sidebar-dark-primary">
                    <h2 class="card-title">Compras</h2>
                    <div class="text-right">
                        <button class="btn btn-dt add"><i class="fa fa-plus"></i> Agregar Compra</button>
                    </div>
                </div>
                <div class="card-body pb-1 mt-2">
                    <table id="crud" class="table table-bordered table-head-fixed table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Proveedor</th>
                                <th>Fecha</th>
                                <th>Neto</th>
                                <th>IVA</th>
                                <th>Total</th>
                                <th style="width: 20%">Opciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <!--  -->
    <div class="modal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <form class="form" action="" method="POST" autocomplete="off">
                <div class="modal-content">
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <div class="card card-secondary m-2">
                            <div class="card-header sidebar-dark-primary">
                                <h2 class="card-title">Datos Generales</h2>
                            </div>
                            <div class="card-body card-body-gray">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="supplier_id">Proveedor</label>
                                            <select id="supplier_id" name="supplier_id"
                                                class="form-control select2 select2-danger select2-supplier"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;" tabindex="-1"
                                                aria-hidden="true">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="type">Tipo Documento</label>
                                            <select id="type" name="type"
                                                class="form-control select2 form-control-sm"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;" tabindex="-1"
                                                aria-hidden="true">
                                                <option value="1">Boleta</option>
                                                <option value="2">Factura</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="number">Número de Documento</label>
                                            <input type="text" name="number"
                                                class="form-control form-control-sm input-sm number">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Fecha:</label>
                                            <div class="input-group date" id="dateDiv" data-target-input="nearest">
                                                <input id="date" name="date" type="text"
                                                    value="{{ date('d-m-Y') }}"
                                                    class="form-control form-control-sm datetimepicker-input"
                                                    data-target="#date">
                                                <div class="input-group-append" data-target="#date"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-secondary m-2">
                            <div class="card-header sidebar-dark-primary">
                                <h2 class="card-title">Detalle de Compra</h2>
                            </div>
                            <div class="card-body card-body-gray">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="product_id_det">Producto</label>
                                            <select id="product_id_det" name="product_id_det"
                                                class="form-control select2 select2-danger select2-product"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;" tabindex="-1"
                                                aria-hidden="true">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-group">
                                            <label for="quantity_det">Cantidad</label>
                                            <input type="text" name="quantity_det" id="quantity_det"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="price_det">Precio Unitario</label>
                                            <input type="text" name="price_det" id="price_det"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="total_det">Total</label>
                                            <input type="text" name="total_det" id="total_det"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <br>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-sm btn-primary btn-add">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-sm table-bordered" id="purchase">
                                            <thead class="sidebar-dark-primary text-white">
                                                <tr>
                                                    <th style="width: 5%">#</th>
                                                    <th>Producto</th>
                                                    <th style="width: 10%">Cantidad</th>
                                                    <th style="width: 15%">Precio U</th>
                                                    <th style="width: 15%">Total</th>
                                                    <th style="width: 5%">Opción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-right" colspan="4">Neto <input type="hidden"
                                                            value="0" id="neto" name="neto"></td>
                                                    <td class="text-right" id="neto_purchase">$0 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right" colspan="4">IVA (19%) <input type="hidden"
                                                            value="0" id="tax" name="tax"></td>
                                                    <td class="text-right" id="iva_purchase">$0 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right" colspan="4">Total <input type="hidden"
                                                            value="0" id="total" name="total"></td>
                                                    <td class="text-right" id="total_purchase">$0 </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-save">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('plugins/input-mask/jquery.inputmask.bundle.min.js') }}"></script>
    <script src="//momentjs.com/downloads/moment.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"
        integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {

            $(".number").inputmask({
                mask: "9{1,30}"
            });

            let token = $('meta[name="csrf-token"]').attr('content');
            let modal = $('.modal');
            let form = $('.form');
            let btnAdd = $('.add'),
                btnSave = $('.btn-save'),
                btnAddProduct = $('.btn-add');

            let neto_purchase = 0;
            let iva_purchase = 0;
            let total_purchase = 0;

            let arrProducts = [];

            var table = $('#crud').DataTable({
                ajax: 'purchase',
                serverSide: true,
                processing: true,
                aaSorting: [
                    [0, "asc"]
                ],
                language: {
                    url: "{{ asset('json/datatable-ES.json') }}",
                },
                dom: 'Bftirp',
                search: {
                    return: true
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'supplier.name',
                        name: 'supplier.name'
                    },
                    {
                        data: 'date',
                        name: 'date',
                        render: function(data, type, row) {
                            var myDate = new Date(data);
                            return myDate.toLocaleDateString('en-GB');
                        }
                    },
                    {
                        data: 'neto',
                        name: 'neto',
                        render: function(data, type, row) {
                            return "$" + parseFloat(data).toLocaleString("de-DE")
                        }
                    },
                    {
                        data: 'tax',
                        name: 'tax',
                        render: function(data, type, row) {
                            return "$" + parseFloat(data).toLocaleString("de-DE")
                        }
                    },
                    {
                        data: 'total',
                        name: 'total',
                        render: function(data, type, row) {
                            return "$" + parseFloat(data).toLocaleString("de-DE")
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        render: function(data, type, row) {
                            html = '<div class="form-group">';
                            html +=
                                '<a class="btn-edit" data-toggle="tooltip" data-placement="top" title="Ver" href="#"><button type="button" class="btn btn-sm btn-dt">Ver</button></a>&nbsp;';
                            html +=
                                '<a class="btn-delete" data-toggle="tooltip" data-placement="top" title="Anular Compra" href="#"><button type="button" class="btn btn-sm btn-dt">Anular Compra</button></a>&nbsp;';
                            return html;
                        }
                    },
                ],
            }).on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    Swal.fire({
                        title: "Favor Esperar",
                        timer: 1000000,
                        timerProgressBar: true,
                        showCloseButton: true,
                        didOpen: function() {
                            Swal.showLoading()
                        }
                    });
                } else {
                    Swal.close();
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            btnAdd.click(function() {
                add();
            })

            btnAddProduct.click(function() {
                addProduct();
            })

            btnSave.click(function(e) {
                e.preventDefault();
                let data = form.serialize();
                data += "&detail=" + JSON.stringify(arrProducts);
                $.ajax({
                    type: "POST",
                    url: "{{ route('purchase.store') }}",
                    data: data + '&_token=' + token,
                    success: function(data) {
                        if (data.status == 200) {
                            table.draw();
                            form.trigger("reset");
                            modal.modal('hide');
                        } else {
                            var error = '';
                            $.each(data.errors, function(key, err_values) {
                                error += err_values
                                error += '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: "Error",
                                html: error
                            })
                        }
                    },
                    error: function(data) {
                        Swal.fire({
                            icon: 'error',
                            title: "Error",
                            html: "Error al Grabar"
                        })
                    }
                }); //end ajax
            })

            $(document).on('click', '.btn-edit', function() {
                btnSave.hide();
                modal.find('.modal-title').text('Ver')

                var rowData = table.row($(this).parents('tr')).data()
                changeScreen(true);
                clearTable();
                clearDetail(true);

                form.find('input[name="id"]').val(rowData.id)

                if (rowData.supplier != null) {
                    var newOption2 = new Option(rowData.supplier.name, rowData.supplier.id, true, true);
                    $('#supplier_id').append(newOption2).trigger('change');
                } else {
                    $('#supplier_id').val(null).trigger('change');
                }

                form.find('input[name="number"]').val(rowData.number)
                var myDate = new Date(rowData.date);
                form.find('input[name="date"]').val(myDate.toLocaleDateString('en-GB').replaceAll("/", "-"))
                form.find('select[name="type"]').val(rowData.type)

                neto_purchase = Math.round(rowData.neto);
                iva_purchase += Math.round(rowData.tax);
                total_purchase += Math.round(rowData.total);
                updateTotal();

                rowData.products.forEach(product => {
                    var htmlTags = '<tr id="tr_' + product.id + '">' +
                        '<td>' + product.id + '</td>' +
                        '<td>' + product.name + '<br><b>Código:</b>' + product.code +
                        '/<b>Marca:</b>' +
                        product.brand.name + '</td>' +
                        '<td class="text-right">' + product.pivot.quantity + '</td>' +
                        '<td class="text-right">$' + parseFloat(product.pivot.price).toLocaleString(
                            "de-DE") +
                        '</td>' +
                        '<td class="text-right"><input type="hidden" id="total_product_' +
                        product.id + '" value="' +
                        product.pivot.total +
                        '"> $' + parseFloat(product.pivot.total).toLocaleString("de-DE") + '</td>' +
                        '<td class="text-center"></td>' +
                        '</tr>';

                    $('#purchase tbody').append(htmlTags);
                });

                modal.modal()
            })


            $(document).on('click', '.btn-delete', function () {
                let id = table.row($(this).parents('tr')).data().id;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/purchase/' + id,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Eliminado', response.message, 'success');
                                    // Opcional: recargar tabla
                                    $('#crud').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Error', 'No se pudo eliminar la compra.', 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'Ocurrió un error en el servidor.', 'error');
                            }
                        });
                    }
                });
            });


            $(document).on('click', '.btn-delete-product', function() {
                let id = $(this).data("param1");
                let total = $("#total_product_" + id).val();
                total = Math.round(total);
                neto_purchase -= Math.round(total - (total * 0.19));
                iva_purchase -= Math.round(total * 0.19);
                total_purchase -= total;
                updateTotal();

                $("#tr_" + id).remove();
            })

            $(document).on('blur', '#quantity_det', function() {
                calculateDetail(1);
            })

            $(document).on('blur', '#price_det', function() {
                calculateDetail(2);
            })

            $(document).on('blur', '#total_det', function() {
                calculateDetail(3);
            })

            function calculateDetail(option) {
                let quantity = ($("#quantity_det").val()).replaceAll("_", "");
                let price = ($("#price_det").val()).replaceAll("_", "");
                let total = ($("#total_det").val()).replaceAll("_", "");
                if (option == 1) {
                    //quantity
                    if (price != 0 && price != "") {
                        total = quantity * price;
                    }
                    if (total != 0 && total != "") {
                        price = total / quantity;
                    }
                } else if (option == 2) {
                    //price
                    if (price != 0 && price != "" && quantity != 0 && quantity != "") {
                        total = quantity * price;
                    }
                } else {
                    //total
                    if (total != 0 && total != "" && quantity != 0 && quantity != "") {
                        price = total / quantity;
                    }
                }

                $("#total_det").val(total);
                $("#price_det").val(price);
            }

            function add() {
                btnSave.show();
                modal.modal()
                form.trigger('reset')
                neto_purchase = 0;
                iva_purchase = 0;
                total_purchase = 0;
                arrProducts = [];
                updateTotal();
                clearDetail(true);
                clearTable();
                changeScreen(false);
            }

            function addProduct() {
                let data = $('#product_id_det').select2('data')
                if (data.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar producto"
                    })
                    return false;
                }
                let product_name = data[0].name;
                let brand = data[0].brand.name;
                let code = data[0].code;
                let product_id = $("#product_id_det").val();
                let quantity = ($("#quantity_det").val()).replaceAll("_", "");
                let price = ($("#price_det").val()).replaceAll("_", "");
                let total = ($("#total_det").val()).replaceAll("_", "");

                if (product_id == 0 || product_id == null) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar producto"
                    })
                    return false;
                }
                if (quantity == 0 || quantity == "") {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar cantidad"
                    })
                    return false;
                }
                if ((price == 0 || price == "") && (total == 0 || total == "")) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar valores"
                    })
                    return false;
                }

                total = Math.round(total);
                neto_purchase += Math.round(total - (total * 0.19));
                iva_purchase += Math.round((total * 0.19));
                total_purchase += Math.round(total);

                let arrProduct = [product_id, quantity, price, total];
                arrProducts.push(arrProduct);

                ///actualizo totales
                updateTotal();
                //limpio detalle
                clearDetail(false)

                var htmlTags = '<tr id="tr_' + product_id + '">' +
                    '<td>' + product_id + '</td>' +
                    '<td>' + product_name + '<br><b>Código:</b>' + code + '/<b>Marca:</b>' + brand + '</td>' +
                    '<td class="text-right">' + quantity + '</td>' +
                    '<td class="text-right">$' + parseFloat(price).toLocaleString("de-DE") + '</td>' +
                    '<td class="text-right"><input type="hidden" id="total_product_' + product_id + '" value="' +
                    total +
                    '"> $' + parseFloat(total).toLocaleString("de-DE") + '</td>' +
                    '<td class="text-center"><button class="btn btn-delete-product" type="button" data-param1="' +
                    product_id + '"><i class="fa-solid fa-trash"></i></button></td>' +
                    '</tr>';

                $('#purchase tbody').append(htmlTags);

            }

            function updateTotal() {
                $("#neto").val(neto_purchase);
                $("#tax").val(iva_purchase);
                $("#total").val(total_purchase);
                $("#neto_purchase").html("$" + parseFloat(neto_purchase).toLocaleString("de-DE"));
                $("#iva_purchase").html("$" + parseFloat(iva_purchase).toLocaleString("de-DE"));
                $("#total_purchase").html("$" + parseFloat(total_purchase).toLocaleString("de-DE"));
            }

            function clearDetail(opc) {
                if (opc) {
                    $('#supplier_id').val(null).trigger('change');
                }
                $('#product_id_det').val(null).trigger('change');
                $("#quantity_det").val("");
                $("#price_det").val("");
                $("#total_det").val("");
            }

            function clearTable() {
                $('#purchase tbody').html("");
            }

            function changeScreen(option) {
                form.find('input').attr("readonly", option);
                form.find('select').attr("disabled", option);
                $("#supplier_id").prop("disabled", option);
                $("#product_id_det").prop("disabled", option);
                $(".btn-add").prop("disabled", option);
            }

            $(".select2-supplier").select2({
                placeholder: "Seleccione una Opcion...",
                dropdownParent: $(".modal"),
                escapeMarkup: function(markup) {
                    return markup;
                }, // let our custom formatter work
                ajax: {
                    type: "GET",
                    url: "/supplier",
                    headers: {
                        "X-CSRF-Token": $("#token").val(),
                    },
                    data: function(params) {
                        var queryParameters = {
                            search: params.term,
                        };
                        return queryParameters;
                    },
                    processResults: function(data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        return {
                            results: data.data,
                        };
                    },
                },
                templateResult: formatData,
                templateSelection: formatDataSelection,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === "") {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true, // add additional parameters
                    };
                },
            });

            $(".select2-product").select2({
                placeholder: "Seleccione una Opcion...",
                dropdownParent: $(".modal"),
                escapeMarkup: function(markup) {
                    return markup;
                }, // let our custom formatter work
                ajax: {
                    type: "GET",
                    url: "/product",
                    headers: {
                        "X-CSRF-Token": $("#token").val(),
                    },
                    data: function(params) {
                        var queryParameters = {
                            search: params.term,
                            start: 0,
                            length: 10
                        };
                        return queryParameters;
                    },
                    processResults: function(data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        return {
                            results: data.data,
                        };
                    },
                },
                templateResult: formatDataProduct,
                templateSelection: formatDataSelection,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === "") {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true, // add additional parameters
                    };
                },
            });

            function formatDataProduct(data) {
                if (data.loading) {
                    return data.text;
                }

                var $container = $(
                    "<div class='row'>" +
                    "<div class='col-6'><b>Nombre:</b> " +
                    data.name +
                    "</div>" +
                    "<div class='col-6'><b>Código:</b> " +
                    data.code +
                    "</div>" +
                    "<div class='col-6'><b>Categoria:</b> " +
                    data.category.name +
                    "</div>" +
                    "<div class='col-6'><b>Marca:</b> " +
                    data.brand.name +
                    "</div>" +
                    "</div>"
                );

                return $container;
            }

            function formatData(data) {
                if (data.loading) {
                    return data.text;
                }

                var $container = $(
                    "<div class='row'>" +
                    "<div class='col-6'>" +
                    data.name +
                    "</div>" +
                    "</div>"
                );

                return $container;
            }

            function formatDataSelection(data) {
                return data.name || data.text;
            }
        })

        $(function() {
            $('#date').datetimepicker({
                locale: 'es',
                format: 'DD-MM-yyyy'
            });

        });
    </script>
@stop
