@extends('adminlte::page')

@section('title', 'Promociones')

@section('content_header')
    &nbsp;
@endsection

@section('css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.css"
        integrity="sha512-ClXpwbczwauhl7XC16/EFu3grIlYTpqTYOwqwAi7rNSqxmTqCpE8VS3ovG+qi61GoxSLnuomxzFXDNcPV1hvCQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection


@section('content')
    <div class="row justify-content-md-center">
        <div class="col">
            <div class="card card-secondary">
                <div class="card-header sidebar-dark-primary">
                    <h2 class="card-title">Promociones</h2>
                    <div class="text-right">
                        <button class="btn btn-dt add"><i class="fa fa-plus"></i> Agregar Promoción</button>
                    </div>
                </div>
                <div class="card-body pb-1 mt-2">
                    <table id="crud" class="table table-bordered table-head-fixed table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Incluye</th>
                                <th style="width: 20%">Opciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--  -->
    <div class="modal " tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
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
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="code">Código</label>
                                            <input type="text" name="code"
                                                class="form-control form-control-sm input-sm">
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Nombre</label>
                                            <input type="text" name="name"
                                                class="form-control form-control-sm input-sm">
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Descripción</label>
                                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Ingrese descripción..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-secondary m-2">
                            <div class="card-header sidebar-dark-primary">
                                <h2 class="card-title">Detalle de Promoción</h2>
                            </div>
                            <div class="card-body card-body-gray">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label for="product_id_det">Producto</label>
                                            <select id="product_id_det" name="product_id_det"
                                                class="form-control select2 select2-danger select2-product"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;" tabindex="-1"
                                                aria-hidden="true">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="quantity_det">Cantidad</label>
                                            <input type="text" name="quantity_det" id="quantity_det"
                                                class="form-control form-control-sm input-sm text-right number">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <br>
                                        <div class="form-group">
                                            <button type="button"
                                                class="btn btn-sm btn-primary btn-add-product">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label for="service_id_det">Servicio</label>
                                            <select id="service_id_det" name="service_id_det"
                                                class="form-control select2 select2-danger select2-service"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;" tabindex="-1"
                                                aria-hidden="true">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">

                                    </div>
                                    <div class="col-2">
                                        <br>
                                        <div class="form-group">
                                            <button type="button"
                                                class="btn btn-sm btn-primary btn-add-service">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-sm table-bordered" id="promotion">
                                            <thead class="sidebar-dark-primary text-white">
                                                <tr>
                                                    <th style="width: 5%">#</th>
                                                    <th>Tipo</th>
                                                    <th>Nombre</th>
                                                    <th style="width: 10%">Cantidad</th>
                                                    <th style="width: 5%">Opción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-save">Guardar</button>
                        <button type="button" class="btn btn-primary btn-update">Modificar</button>
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
            var token = $('meta[name="csrf-token"]').attr('content');
            var modal = $('.modal');
            var form = $('.form');
            var btnAdd = $('.add'),
                btnSave = $('.btn-save'),
                btnUpdate = $('.btn-update'),
                btnAddProduct = $('.btn-add-product'),
                btnAddService = $('.btn-add-service');

            let arrProducts = [];
            let arrServices = [];

            var table = $('#crud').DataTable({
                ajax: 'promotion',
                serverSide: true,
                processing: true,
                aaSorting: [
                    [0, "asc"]
                ],
                language: {
                    url: "{{ asset('json/datatable-ES.json') }}",
                },
                dom: 'Bftirp',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'id',
                        name: 'include',
                        render: function(data, type, row) {
                            html = '<b>Productos:</b><br>';
                            row.products.forEach(element => {
                                html += '-' + element.name + '(' + element.pivot.quantity +
                                    ')<br>';
                            });
                            html += '<b>Servicios:</b><br>';
                            row.services.forEach(element => {
                                html += '-' + element.name + '<br>';
                            });
                            return html;
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        render: function(data, type, row) {
                            html = '<div class="form-group">';
                            html +=
                                '<a class="btn-edit" data-toggle="tooltip" data-placement="top" title="Modificar" href="#"><button type="button" class="btn btn-sm btn-dt">Modificar</button></a>&nbsp;';

                            html +=
                                '<button data-toggle="tooltip" data-placement="top" title="Eliminar" type="button" class="btn btn-sm btn-dt btn-delete"> Eliminar</button>';
                            html += '</div>';
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

            btnAddService.click(function() {
                addService();
            })

            btnSave.click(function(e) {
                e.preventDefault();
                var data = form.serialize()
                data += "&products=" + JSON.stringify(arrProducts);
                data += "&services=" + JSON.stringify(arrServices);
                $.ajax({
                    type: "POST",
                    url: "{{ route('promotion.store') }}",
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
                btnUpdate.show();

                modal.find('.modal-footer button[type="submit"]').text('Update')

                let rowData = table.row($(this).parents('tr')).data()

                form.find('input[name="id"]').val(rowData.id)
                form.find('input[name="code"]').val(rowData.code)
                form.find('input[name="name"]').val(rowData.name)
                form.find('textarea[name="description"]').val(rowData.description)

                clearDetail();
                clearTable();
                arrProducts = [];
                arrServices = [];

                rowData.products.forEach(product => {
                    let arrProduct = [product.id, product.pivot.quantity];
                    arrProducts.push(arrProduct);

                    let htmlTags = '<tr id="tr_p_' + product.id + '">' +
                        '<td>' + product.id + '</td>' +
                        '<td>Producto</td>' +
                        '<td>' + product.name + '<br><b>Código:</b>' + product.code +
                        '</td>' +
                        '<td class="text-right">' + product.pivot.quantity + '</td>' +
                        '<td class="text-center"><button class="btn btn-delete-product" type="button" data-param1="' +
                        product.id + '" data-param2="' +
                        product.pivot.quantity +
                        '"><i class="fa-solid fa-trash"></i></button></td>' +
                        '</tr>';

                    $('#promotion tbody').append(htmlTags);
                });

                rowData.services.forEach(service => {
                    let arrService = [service.id];
                    arrServices.push(arrService);

                    let htmlTags = '<tr id="tr_p_' + service.id + '">' +
                        '<td>' + service.id + '</td>' +
                        '<td>Servicio</td>' +
                        '<td>' + service.name + '<br><b>Código:</b>' + service.code +
                        '</td>' +
                        '<td class="text-right">-</td>' +
                        '<td class="text-center"><button class="btn btn-delete-service" type="button" data-param1="' +
                        service.id + '" ><i class="fa-solid fa-trash"></i></button></td>' +
                        '</tr>';

                    $('#promotion tbody').append(htmlTags);
                });

                modal.modal()
            })

            $(document).on('click', '.btn-delete-product', function() {
                let id = $(this).data("param1");
                let index = 0;
                let pos = 0;
                arrProducts.forEach(element => {
                    if (element[0] == id) {
                        pos = index;
                    }
                    index++;
                });
                arrProducts.splice(pos, 1);
                $("#tr_p_" + id).remove();
            })

            $(document).on('click', '.btn-delete-service', function() {
                let id = $(this).data("param1");
                let index = 0;
                let pos = 0;
                arrServices.forEach(element => {
                    if (element[0] == id) {
                        pos = index;
                    }
                    index++;
                });
                arrServices.splice(pos, 1);
                $("#tr_s_" + id).remove();
            })

            btnUpdate.click(function() {
                var formData = form.serialize() + '&_method=PUT&_token=' + token
                formData += "&products=" + JSON.stringify(arrProducts);
                formData += "&services=" + JSON.stringify(arrServices);
                var updateId = form.find('input[name="id"]').val()
                $.ajax({
                    type: "POST",
                    url: "/promotion/" + updateId,
                    data: formData,
                    success: function(data) {
                        if (data.status == 200) {
                            table.draw();
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

            $(document).on('click', '.btn-delete', function() {
                var rowid = $(this).data('rowid')
                var el = $(this)
                if (!rowid) return;

                Swal.fire({
                    title: "Esta seguro de eliminar el registro?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si",
                    cancelButtonText: "No",
                    showCloseButton: true
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            dataType: 'JSON',
                            url: "/promotion/" + rowid,
                            data: {
                                _method: 'delete',
                                _token: token
                            },
                            success: function(data) {
                                if (data.status == 200) {
                                    table.row(el.parents('tr'))
                                        .remove()
                                        .draw();
                                }
                            }
                        }); //end ajax
                    }
                });
            })

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

            $(".select2-service").select2({
                placeholder: "Seleccione una Opcion...",
                dropdownParent: $(".modal"),
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

            function add() {
                modal.modal()
                form.trigger('reset')
                btnSave.show();
                btnUpdate.hide()
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

                let arrProduct = [product_id, quantity];
                arrProducts.push(arrProduct);

                //limpio detalle
                clearDetail();

                var htmlTags = '<tr id="tr_p_' + product_id + '">' +
                    '<td>' + product_id + '</td>' +
                    '<td>Producto</td>' +
                    '<td>' + product_name + '<br><b>Código:</b>' + code + '/<b>Marca:</b>' + brand + '</td>' +
                    '<td class="text-right">' + quantity + '</td>' +
                    '<td class="text-center"><button class="btn btn-delete-product" type="button" data-param1="' +
                    product_id + '"><i class="fa-solid fa-trash"></i></button></td>' +
                    '</tr>';

                $('#promotion tbody').append(htmlTags);

            }

            function addService() {
                let data = $('#service_id_det').select2('data')
                if (data.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar servicio"
                    })
                    return false;
                }
                let service_name = data[0].name;
                let code = data[0].code;
                let service_id = $("#service_id_det").val();

                if (service_id == 0 || service_id == null) {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        html: "Debe agregar servicio"
                    })
                    return false;
                }

                let arrService = [service_id];
                arrServices.push(arrService);

                //limpio detalle
                clearDetail();

                var htmlTags = '<tr id="tr_s_' + service_id + '">' +
                    '<td>' + service_id + '</td>' +
                    '<td>Servicio</td>' +
                    '<td>' + service_name + '<br><b>Código:</b>' + code + '</td>' +
                    '<td class="text-right">-</td>' +
                    '<td class="text-center"><button class="btn btn-delete-service" type="button" data-param1="' +
                    service_id + '"><i class="fa-solid fa-trash"></i></button></td>' +
                    '</tr>';

                $('#promotion tbody').append(htmlTags);

            }

            function clearDetail() {
                $('#service_id_det').val(null).trigger('change');
                $('#product_id_det').val(null).trigger('change');
                $("#quantity_det").val("");
            }

            function clearTable() {
                $('#promotion tbody').html("");
            }
        })
    </script>
@stop
