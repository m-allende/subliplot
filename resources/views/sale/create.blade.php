@extends('adminlte::page')

@section('title', 'Ventas')

@section('content_header')
    <h1>Crear Venta</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-1"></div>
        <div class="card col-10">
            <form class="form" action="" method="POST" autocomplete="off">
                <div class="card-body">
                    <input type="hidden" name="id">
                    <div class="card card-secondary m-2">
                        <div class="card-header sidebar-dark-primary">
                            <h2 class="card-title">Datos Generales</h2>
                        </div>
                        <div class="card-body card-body-gray">
                            <div class="row">
                                <div class="col-10">
                                    <div class="form-group">
                                        <label for="client_id">Cliente</label>
                                        <select id="client_id" name="client_id"
                                            class="form-control select2 select2-danger select2-client"
                                            data-dropdown-css-class="select2-danger" style="width: 100%;" tabindex="-1"
                                            aria-hidden="true">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <br>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-sm btn-primary btn-add-client">Crear
                                            Cliente</button>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="type">Tipo Documento</label>
                                        <select id="type" name="type" class="form-control select2 form-control-sm"
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
                                            <input id="date" name="date" type="text" value="{{ date('d-m-Y') }}"
                                                class="form-control form-control-sm datetimepicker-input"
                                                data-target="#date">
                                            <div class="input-group-append" data-target="#date"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="number">Descripción de lo realizado</label>
                                        <textarea class="form-control" name="observation" id="observation" cols="30" rows="5">
Marca:
Modelo:
Patente:
-----------------------------------------------------------------------------------------------</textarea>
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
                            <div id="detail_sale">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="promotion_id_det">Promoción</label>
                                            <select id="promotion_id_det" name="promotion_id_det"
                                                class="form-control select2 select2-danger select2-promotion"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;" tabindex="-1"
                                                aria-hidden="true">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="promotion_price_det">Precio Venta</label>
                                            <input type="text" name="promotion_price_det" id="promotion_price_det"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-group">
                                            <label for="promotion_quantity_det">Cantidad</label>
                                            <input type="text" name="promotion_quantity_det" id="promotion_quantity_det"
                                                value="1"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="promotion_total_det">Total</label>
                                            <input type="text" name="promotion_total_det" id="promotion_total_det"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <br>
                                        <div class="form-group">
                                            <button type="button"
                                                class="btn btn-sm btn-primary btn-add-promotion">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="service_id_det">Servicio</label>
                                            <select id="service_id_det" name="service_id_det"
                                                class="form-control select2 select2-danger select2-service"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;"
                                                tabindex="-1" aria-hidden="true">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="service_price_det">Precio Venta</label>
                                            <input type="text" name="service_price_det" id="service_price_det"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-group">
                                            <label for="service_quantity_det">Cantidad</label>
                                            <input type="text" name="service_quantity_det" id="service_quantity_det"
                                                value="1"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="service_total_det">Total</label>
                                            <input type="text" name="service_total_det" id="service_total_det"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <br>
                                        <div class="form-group">
                                            <button type="button"
                                                class="btn btn-sm btn-primary btn-add-service">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="product_id_det">Producto</label>
                                            <select id="product_id_det" name="product_id_det"
                                                class="form-control select2 select2-danger select2-product"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;"
                                                tabindex="-1" aria-hidden="true">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-group">
                                            <label for="stock_det">Stock</label>
                                            <input type="text" name="stock_det" id="stock_det" readonly="readonly"
                                                class="form-control form-control-sm input-sm text-right">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="price_det">Precio Venta</label>
                                            <input type="text" name="price_det" id="price_det"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-group">
                                            <label for="quantity_det">Cantidad</label>
                                            <input type="text" name="quantity_det" id="quantity_det" value="1"
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
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-sm table-bordered" id="sale">
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
                                                <td class="text-right" colspan="4">Sub-Total <input type="hidden"
                                                        value="0" id="subtotal" name="subtotal"></td>
                                                <td class="text-right" id="sub_total_sale">$0 </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="4">Descuento</td>
                                                <td class="text-right" id="discount_sale"><input type="text"
                                                        class="form-control form-control-sm input-sm text-right number"
                                                        name="discount" id="discount" value="0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="4">Neto <input type="hidden"
                                                        value="0" id="neto" name="neto"></td>
                                                <td class="text-right" id="neto_sale">$0 </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="4">IVA (19%) <input type="hidden"
                                                        value="0" id="tax" name="tax"></td>
                                                <td class="text-right" id="iva_sale">$0 </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="4">Total <input type="hidden"
                                                        value="0" id="total" name="total"></td>
                                                <td class="text-right" id="total_sale">$0 </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="button" class="btn btn-primary btn-save">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" data-backdrop="static" id="modal_new">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body" id="print">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-print">Imprimir</button>
                    <button type="button" class="btn btn-secondary btn-close-voucher">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" id="modalClient">
        <div class="modal-dialog" role="document">
            <form class="form form-client" action="" method="POST" autocomplete="off">
                <div class="modal-content">
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <div class="card card-secondary m-2">
                            <div class="card-header sidebar-dark-primary">
                                <h2 class="card-title">Datos Generales</h2>
                            </div>
                            <div class="card-body card-body-gray">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="identification">Identificación</label>
                                            <input type="text" name="identification"
                                                class="form-control form-control-sm input-sm">
                                        </div>
                                        <div class="form-group">
                                            <label for="type">Tipo</label>
                                            <select id="type" name="type"
                                                class="form-control select2 form-control-sm"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;" tabindex="-1"
                                                aria-hidden="true">
                                                <option value="1">Persona</option>
                                                <option value="2">Empresa</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label id ="lblName" for="name">Nombre</label>
                                            <input type="text" name="name"
                                                class="form-control form-control-sm input-sm">
                                        </div>

                                        <div class="form-group">
                                            <label for="address">Dirección</label>
                                            <input type="text" class="form-control form-control-sm" id="address"
                                                name="address" placeholder="Dirección" value="">
                                            <input name="addressLatitude" type="hidden" class="form-control"
                                                id="addressLatitude" value="">
                                            <input name="addressLongitude" type="hidden" class="form-control"
                                                id="addressLongitude" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Telefono</label>
                                            <input type="text" name="phone"
                                                class="form-control form-control-sm input-sm">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="text" name="email"
                                                class="form-control form-control-sm input-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-save-client">Guardar</button>
                        <button type="button" class="btn btn-primary btn-update-client">Modificar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')

    <script>
        $(document).ready(function() {

            $(".number").inputmask({
                mask: "9{1,30}"
            });

            let token = $('meta[name="csrf-token"]').attr('content');
            let form = $('.form');
            let formClient = $('.form-client');
            let btnSave = $('.btn-save'),
                btnAddProduct = $('.btn-add'),
                btnAddPromotion = $('.btn-add-promotion'),
                btnAddService = $('.btn-add-service'),
                btnAddClient = $('.btn-add-client'),
                btnSaveClient = $('.btn-save-client'),
                btnUpdateClient = $('.btn-update-client'),
                btnPrint = $('.btn-print'),
                btnCloseVoucher = $('.btn-close-voucher');

            let modalClient = $('#modalClient');

            let neto_sale = 0;
            let iva_sale = 0;
            let sub_total_sale = 0;
            let total_sale = 0;

            let arrProducts = [];
            let arrPromotions = [];
            let arrServices = [];

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            btnAddProduct.click(function() {
                addProduct();
            })

            btnAddPromotion.click(function() {
                addPromotion();
            })

            btnAddService.click(function() {
                addService();
            })

            btnAddClient.click(function() {
                addClient();
            })

            btnSave.click(function(e) {
                e.preventDefault();
                let id = 0;
                let data = form.serialize();
                data += "&products=" + JSON.stringify(arrProducts);
                data += "&services=" + JSON.stringify(arrServices);
                data += "&promotions=" + JSON.stringify(arrPromotions);
                $.ajax({
                    type: "POST",
                    url: "{{ route('sale.store') }}",
                    data: data + '&_token=' + token,
                    success: function(data) {
                        if (data.status == 200) {
                            id = data.sale.id;
                            Swal.fire({
                                icon: 'success',
                                title: "Grabado",
                                html: "Grabado Correctamente"
                            })

                            Swal.fire({
                                icon: 'success',
                                title: "Grabado Correctamente",
                                html: "Desea Imprimir la boleta?",
                                showDenyButton: true,
                                showCancelButton: true,
                                confirmButtonText: 'Si',
                                denyButtonText: 'No'
                            }).then((result) => {
                                /* Read more about isConfirmed, isDenied below */
                                if (result.isConfirmed) {
                                    $.ajax({
                                        type: "GET",
                                        url: "/sale/" + id + "/edit",
                                        data: '_token=' + token,
                                        success: function(data) {
                                            $("#print").html(data);
                                            $("#modal_new").modal('show');
                                        },
                                        error: function(data) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: "Error",
                                                html: "Error al Grabar"
                                            })
                                        }
                                    }); //end ajax

                                }
                            });
                            clearTable();
                            clearDetail(true);
                            updateTotal();
                            form.trigger("reset");
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

            btnSaveClient.click(function(e) {
                e.preventDefault();
                var data = form.serialize()
                console.log(data)
                $.ajax({
                    type: "POST",
                    url: "{{ route('client.store') }}",
                    data: data + '&_token=' + token,
                    success: function(data) {
                        if (data.status == 200) {
                            //table.draw();
                            $("#client_id").append(new Option(data.client.name, data.client.id, true, true)).trigger(
                                'change');
                            formClient.trigger("reset");
                            modalClient.modal('hide');
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

            btnPrint.click(function(e) {
                let content = $("#print").html();
                let contentOriginal = document.body.innerHTML;
                document.body.innerHTML = content;
                window.print();
                document.body.innerHTML = contentOriginal;
            })

            btnCloseVoucher.click(function(e) {
                $("#modal_new").modal('hide');
            })

            $(document).on('click', '.btn-delete-product', function() {
                let id = $(this).data("param1");
                let total = $("#total_product_" + id).val();

                total = Math.round(total);
                neto_sale -= Math.round(total - (total * 0.19));
                iva_sale -= Math.round(total * 0.19);
                sub_total_sale -= total;
                updateTotal();

                $("#tr_" + id).remove();

                arrProducts.forEach(function(element, index) {
                    if (element[0] == id && element[3] == total) {
                        arrProducts.splice(index, 1);
                    }
                });

            })

            $(document).on('click', '.btn-delete-service', function() {
                let id = $(this).data("param1");
                let total = $("#total_service_" + id).val();
                total = Math.round(total);
                neto_sale -= Math.round(total - (total * 0.19));
                iva_sale -= Math.round(total * 0.19);
                sub_total_sale -= total;
                updateTotal();

                $("#tr_ser_" + id).remove();

                arrServices.forEach(function(element, index) {
                    if (element[0] == id && element[3] == total) {
                        arrServices.splice(index, 1);
                    }
                });
            })

            $(document).on('click', '.btn-delete-promotion', function() {
                let id = $(this).data("param1");
                let total = $("#total_promotion_" + id).val();
                total = Math.round(total);
                neto_sale -= Math.round(total - (total * 0.19));
                iva_sale -= Math.round(total * 0.19);
                sub_total_sale -= total;
                updateTotal();

                $("#tr_pr_" + id).remove();

                arrPromotions.forEach(function(element, index) {
                    if (element[0] == id && element[3] == total) {
                        arrPromotions.splice(index, 1);
                    }
                });

                arrServices.forEach(function(element, index) {
                    if (element[4] == id && element[3] == 0) {
                        arrServices.splice(index, 1);
                    }
                });

                arrProducts.forEach(function(element, index) {
                    if (element[4] == id && element[3] == 0) {
                        arrProducts.splice(index, 1);
                    }
                });
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

            $(document).on('blur', '#promotion_quantity_det', function() {
                calculatePromotionDetail(1);
            })

            $(document).on('blur', '#promotion_price_det', function() {
                calculatePromotionDetail(2);
            })

            $(document).on('blur', '#promotion_total_det', function() {
                calculatePromotionDetail(3);
            })

            $(document).on('blur', '#service_quantity_det', function() {
                calculateServiceDetail(1);
            })

            $(document).on('blur', '#service_price_det', function() {
                calculateServiceDetail(2);
            })

            $(document).on('blur', '#service_total_det', function() {
                calculateServiceDetail(3);
            })

            $(document).on('blur', '#discount', function() {
                updateTotal();
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

            function calculatePromotionDetail(option) {
                let quantity = ($("#promotion_quantity_det").val()).replaceAll("_", "");
                let price = ($("#promotion_price_det").val()).replaceAll("_", "");
                let total = ($("#promotion_total_det").val()).replaceAll("_", "");
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

                $("#promotion_total_det").val(total);
                $("#promotion_price_det").val(price);
            }

            function calculateServiceDetail(option) {
                let quantity = ($("#service_quantity_det").val()).replaceAll("_", "");
                let price = ($("#service_price_det").val()).replaceAll("_", "");
                let total = ($("#service_total_det").val()).replaceAll("_", "");
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

                $("#service_total_det").val(total);
                $("#service_price_det").val(price);
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
                let stock = $("#stock_det").val();

                if (product_id == 0 || product_id == null) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar producto"
                    })
                    return false;
                }
                if (stock <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "No hay stock de ese producto"
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

                if (quantity > stock) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "La Cantidad solicitada supera el stock del producto"
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

                /*
                total = Math.round(total);
                neto_sale += Math.round(total - (total * 0.19));
                iva_sale += Math.round((total * 0.19));
                */
                sub_total_sale += Math.round(total);

                let arrProduct = [product_id, quantity, price, total, 0];
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

                $('#sale tbody').append(htmlTags);

            }

            function addPromotion() {
                let data = $('#promotion_id_det').select2('data')
                if (data.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar promoción"
                    })
                    return false;
                }
                let promotion_name = data[0].name;
                let code = data[0].code;
                let promotion_id = $("#promotion_id_det").val();
                let quantity = ($("#promotion_quantity_det").val()).replaceAll("_", "");
                let price = ($("#promotion_price_det").val()).replaceAll("_", "");
                let total = ($("#promotion_total_det").val()).replaceAll("_", "");


                if (promotion_id == 0 || promotion_id == null) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar promoción"
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
                /*
                total = Math.round(total);
                neto_sale += Math.round(total - (total * 0.19));
                iva_sale += Math.round((total * 0.19));
                */
                sub_total_sale += Math.round(total);


                let detail = "<b>Productos:</b><br> ";
                data[0].products.forEach(element => {
                    detail += "-" + element.name + " / " + element.code + "<br>";
                    let arrProduct = [element.id, element.pivot.quantity, 0, 0, promotion_id];
                    arrProducts.push(arrProduct);
                });

                detail += "<b>Servicios:</b><br> ";
                data[0].services.forEach(element => {
                    detail += "-" + element.name + " / " + element.code + "<br>";
                    let arrService = [element.id, element.pivot.quantity, 0, 0, promotion_id];
                    arrServices.push(arrService);
                });

                let arrPromotion = [promotion_id, quantity, price, total];
                arrPromotions.push(arrPromotion);

                ///actualizo totales
                updateTotal();
                //limpio detalle
                clearDetail(false)

                var htmlTags = '<tr id="tr_pr_' + promotion_id + '">' +
                    '<td>' + promotion_id + '</td>' +
                    '<td>' + promotion_name + '<br><b>Código:</b>' + code + '/<b><br>Detalle:</b><br>' + detail +
                    '</td>' +
                    '<td class="text-right">' + quantity + '</td>' +
                    '<td class="text-right">$' + parseFloat(price).toLocaleString("de-DE") + '</td>' +
                    '<td class="text-right"><input type="hidden" id="total_promotion_' + promotion_id +
                    '" value="' +
                    total +
                    '"> $' + parseFloat(total).toLocaleString("de-DE") + '</td>' +
                    '<td class="text-center"><button class="btn btn-delete-promotion" type="button" data-param1="' +
                    promotion_id + '"><i class="fa-solid fa-trash"></i></button></td>' +
                    '</tr>';

                $('#sale tbody').append(htmlTags);

            }

            function addService() {
                let data = $('#service_id_det').select2('data')
                if (data.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar Servicio"
                    })
                    return false;
                }
                let service_name = data[0].name;
                let code = data[0].code;
                let service_id = $("#service_id_det").val();
                let quantity = ($("#service_quantity_det").val()).replaceAll("_", "");
                let price = ($("#service_price_det").val()).replaceAll("_", "");
                let total = ($("#service_total_det").val()).replaceAll("_", "");


                if (service_id == 0 || service_id == null) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar servicio"
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
                /*
                total = Math.round(total);
                neto_sale += Math.round(total - (total * 0.19));
                iva_sale += Math.round((total * 0.19));
                */
                sub_total_sale += Math.round(total);

                let arrService = [service_id, quantity, price, total, 0];
                arrServices.push(arrService);

                ///actualizo totales
                updateTotal();
                //limpio detalle
                clearDetail(false)

                var htmlTags = '<tr id="tr_ser_' + service_id + '">' +
                    '<td>' + service_id + '</td>' +
                    '<td>' + service_name + '<br><b>Código:</b>' + code + '/<b>' +
                    '</td>' +
                    '<td class="text-right">' + quantity + '</td>' +
                    '<td class="text-right">$' + parseFloat(price).toLocaleString("de-DE") + '</td>' +
                    '<td class="text-right"><input type="hidden" id="total_service_' + service_id +
                    '" value="' +
                    total +
                    '"> $' + parseFloat(total).toLocaleString("de-DE") + '</td>' +
                    '<td class="text-center"><button class="btn btn-delete-service" type="button" data-param1="' +
                    service_id + '"><i class="fa-solid fa-trash"></i></button></td>' +
                    '</tr>';

                $('#sale tbody').append(htmlTags);

            }

            function addClient() {
                modalClient.modal()
                formClient.trigger('reset')
                modalClient.find('.card-title').text('Agregar Nuevo')
                btnSaveClient.show();
                btnUpdateClient.hide()
            }

            function updateTotal() {
                if (sub_total_sale == 0) {
                    $("#discount").val(0);
                }

                if ($("#discount").val() != 0) {
                    total_sale = sub_total_sale - Math.round($("#discount").val());
                } else {
                    total_sale = sub_total_sale;
                }
                iva_sale = total_sale * 0.19;
                neto_sale = total_sale - iva_sale;

                $("#neto").val(neto_sale);
                $("#tax").val(iva_sale);
                $("#sub_total").val(sub_total_sale);
                $("#neto_sale").html("$" + parseFloat(neto_sale).toLocaleString("de-DE"));
                $("#iva_sale").html("$" + parseFloat(iva_sale).toLocaleString("de-DE"));
                $("#sub_total_sale").html("$" + parseFloat(sub_total_sale).toLocaleString("de-DE"));

                $("#total").val(total_sale);
                $("#total_sale").html("$" + parseFloat(total_sale).toLocaleString("de-DE"));
            }

            function clearDetail(opc) {
                if (opc) {
                    $('#client_id').val(null).trigger('change');
                    neto_sale = 0;
                    iva_sale = 0;
                    sub_total_sale = 0;
                    total_sale = 0;
                    arrProducts = [];
                    arrServices = [];
                    arrPromotions = [];
                }

                //promociones
                $('#promotion_id_det').val(null).trigger('change');
                $("#promotion_quantity_det").val("1");
                $("#promotion_price_det").val("");
                $("#promotion_total_det").val("");
                //servicios
                $('#service_id_det').val(null).trigger('change');
                $("#service_quantity_det").val("1");
                $("#service_price_det").val("");
                $("#service_total_det").val("");
                //productos
                $('#product_id_det').val(null).trigger('change');
                $("#quantity_det").val("1");
                $("#price_det").val("");
                $("#total_det").val("");
                $("#stock_det").val("");
            }

            function clearTable() {
                $('#sale tbody').html("");
            }

            function changeScreen(option) {
                form.find('input').attr("readonly", option);
                //productos
                form.find('input[name="stock_det"]').attr("readonly", true);
                form.find('input[name="price_det"]').attr("readonly", true);
                form.find('input[name="total_det"]').attr("readonly", true);
                //promociones
                form.find('input[name="promotion_price_det"]').attr("readonly", true);
                form.find('input[name="promotion_total_det"]').attr("readonly", true);
                //servicios
                form.find('input[name="service_price_det"]').attr("readonly", true);
                form.find('input[name="service_total_det"]').attr("readonly", true);

                form.find('select').attr("disabled", option);
                $("#client_id").prop("disabled", option);
                $("#product_id_det").prop("disabled", option);
                $(".btn-add").prop("disabled", option);
                $(".btn-add-promotion").prop("disabled", option);
                $(".btn-add-service").prop("disabled", option);

                //div compra
                (option ? $("#detail_sale").hide() : $("#detail_sale").show());

            }

            $(".select2-client").select2({
                placeholder: "Seleccione una Opcion...",
                escapeMarkup: function(markup) {
                    return markup;
                }, // let our custom formatter work
                ajax: {
                    type: "GET",
                    url: "/client",
                    headers: {
                        "X-CSRF-Token": $("#token").val(),
                    },
                    data: function(params) {
                        var queryParameters = {
                            search: params.term,
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

            $(".select2-promotion").select2({
                placeholder: "Seleccione una Opcion...",
                escapeMarkup: function(markup) {
                    return markup;
                }, // let our custom formatter work
                ajax: {
                    type: "GET",
                    url: "/promotion",
                    headers: {
                        "X-CSRF-Token": $("#token").val(),
                    },
                    data: function(params) {
                        var queryParameters = {
                            search: params.term,
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
                templateResult: formatDataPromotion,
                templateSelection: formatDataSelectionPromotion,
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

            $(".select2-service").select2({
                placeholder: "Seleccione una Opcion...",
                escapeMarkup: function(markup) {
                    return markup;
                }, // let our custom formatter work
                ajax: {
                    type: "GET",
                    url: "/service",
                    headers: {
                        "X-CSRF-Token": $("#token").val(),
                    },
                    data: function(params) {
                        var queryParameters = {
                            search: params.term,
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
                templateResult: formatDataService,
                templateSelection: formatDataSelectionService,
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
                templateSelection: formatDataSelectionProduct,
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
                    "<div class='col-6'><b>Presentación:</b> " +
                    data.presentation.name +
                    "</div>" +
                    "<div class='col-6'><b>stock:</b> " +
                    data.stock +
                    "</div>" +
                    "</div>"
                );

                return $container;
            }

            function formatDataPromotion(data) {
                if (data.loading) {
                    return data.text;
                }
                let detail = "<b>Productos:</b><br> ";
                data.products.forEach(element => {
                    detail += "-" + element.name + " / " + element.code + "<br>";
                });
                detail += "<b>Servicios:</b><br> ";
                data.services.forEach(element => {
                    detail += "-" + element.name + " / " + element.code + "<br>";
                });

                var $container = $(
                    "<div class='row'>" +
                    "<div class='col-12'><b>Nombre:</b> " +
                    data.name +
                    "</div>" +
                    "<div class='col-12'><b>Código:</b> " +
                    data.code +
                    "</div>" +
                    "<div class='col-12'><b>Detalle:</b><br> " +
                    detail +
                    "</div>" +
                    "</div>"
                );

                return $container;
            }

            function formatDataService(data) {
                if (data.loading) {
                    return data.text;
                }
                var $container = $(
                    "<div class='row'>" +
                    "<div class='col-12'><b>Nombre:</b> " +
                    data.name +
                    "</div>" +
                    "<div class='col-12'><b>Código:</b> " +
                    data.code +
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

            function formatDataSelectionProduct(data) {
                if (data.name) {
                    $("#stock_det").val(data.stock);
                    $("#price_det").val(data.last_price.price);
                    $("#total_det").val(data.last_price.price * $("#quantity_det").val());
                }
                return data.name || data.text;
            }

            function formatDataSelectionPromotion(data) {
                if (data.name) {
                    $("#promotion_price_det").val(data.last_price.price);
                    $("#promotion_total_det").val(data.last_price.price * $("#promotion_quantity_det").val());
                }
                return data.name || data.text;
            }

            function formatDataSelectionService(data) {
                if (data.name) {
                    $("#service_price_det").val(data.last_price.price);
                    $("#service_total_det").val(data.last_price.price * $("#service_quantity_det").val());
                }
                return data.name || data.text;
            }
        })

        $(function() {
            $('#date').datetimepicker({
                language: 'es',
                format: 'DD-MM-yyyy'
            });

        });
    </script>
@stop
