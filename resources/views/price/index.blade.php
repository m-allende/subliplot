@extends('adminlte::page')

@section('title', 'Precios')

@section('content_header')
    &nbsp;
@endsection

@section('content')
    <div class="row justify-content-md-center">
        <div class="col">
            <div class="card card-secondary">
                <div class="card-header sidebar-dark-primary">
                    <h2 class="card-title">Precios</h2>
                </div>
                <div class="card-body pb-1 mt-2">
                    <table id="crud" class="table table-bordered table-head-fixed table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Stock</th>
                                <th>Valor ($)</th>
                                <th style="width: 20%">Opciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
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
                                            <input type="text" name="code" @readonly(true)
                                                class="form-control form-control-sm input-sm ">
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Nombre</label>
                                            <input type="text" name="name" @readonly(true)
                                                class="form-control form-control-sm input-sm">
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Descripción</label>
                                            <textarea @readonly(true) id="description" name="description" class="form-control" rows="3"
                                                placeholder="Ingrese descripción..."></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="price">Valor de Venta (unidad)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fa-solid fa-dollar-sign"></i></span>
                                                </div>
                                                <input type="text" name="price" id="price"
                                                    class="form-control form-control-sm input-sm text-right number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
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
            var btnUpdate = $('.btn-update');

            var table = $('#crud').DataTable({
                ajax: 'price',
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
                        data: 'parent_type',
                        name: 'parent_type',
                        render: function(data, type, row) {
                            switch (data) {
                                case "App\\Models\\Product":
                                    return "Producto";
                                    break;
                                case "App\\Models\\Service":
                                    return "Servicio";
                                    break;
                                case "App\\Models\\Promotion":
                                    return "Promoción";
                                    break;
                            }
                        }
                    },
                    {
                        data: 'parent.name',
                        name: 'parent.name'
                    },
                    {
                        data: 'parent.description',
                        name: 'parent.description'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'price',
                        name: 'price',
                        render: function(data, type, row) {
                            return "$" + parseFloat(data).toLocaleString("de-DE");
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        render: function(data, type, row) {
                            html = '<div class="form-group">';
                            html +=
                                '<a class="btn-edit" data-toggle="tooltip" data-placement="top" title="Modificar" href="#"><button type="button" class="btn btn-sm btn-dt">Modificar Valor</button></a>&nbsp;';
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

            $(document).on('click', '.btn-edit', function() {
                btnUpdate.show();
                modal.find('.card-title').text('Modificar')
                modal.find('.modal-footer button[type="submit"]').text('Update')

                var rowData = table.row($(this).parents('tr')).data()

                form.find('input[name="id"]').val(rowData.id)
                form.find('input[name="code"]').val(rowData.parent.code)
                form.find('input[name="name"]').val(rowData.parent.name)
                form.find('textarea[name="description"]').val(rowData.parent.description)
                form.find('input[name="price"]').val(rowData.price)
                modal.modal()
            })

            btnUpdate.click(function() {
                var formData = form.serialize() + '&_method=PUT&_token=' + token
                var updateId = form.find('input[name="id"]').val()
                $.ajax({
                    type: "POST",
                    url: "/price/" + updateId,
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
        })
    </script>
@stop
