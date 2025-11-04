@extends('adminlte::page')

@section('title', 'Servicios')

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
                    <h2 class="card-title">Servicios</h2>
                    <div class="text-right">
                        <button class="btn btn-dt add"><i class="fa fa-plus"></i> Agregar Servicio</button>
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
                                <th style="width: 20%">Opciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>



    <!--  -->
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
                                    <div class="col-12">
                                        <div class="upload-msg">
                                            Subir imagen para comenzar a cortar
                                        </div>
                                        <div class="upload-demo-wrap" style="display:none">
                                            <div id="upload-demo"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="actions">
                                            <a class="btn btn-primary file-btn">
                                                <span>Nueva Imagen</span>
                                                <input type="file" id="upload" value="Elegir una imagen"
                                                    accept="image/*" />
                                            </a>
                                            <button type="button" class="btn btn-primary upload-result">Guardar</button>
                                        </div>
                                    </div>
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
                btnUpdate = $('.btn-update');

            let image = "";

            var $uploadCrop;

            function readFile(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('.upload-demo').addClass('ready');
                        $uploadCrop.croppie('bind', {
                            url: e.target.result
                        }).then(function() {
                            console.log('jQuery bind complete');
                        });
                        $(".upload-demo-wrap").show();
                        $(".upload-msg").hide();
                    }

                    reader.readAsDataURL(input.files[0]);
                } else {
                    swal("Sorry - you're browser doesn't support the FileReader API");
                }
            }

            $uploadCrop = $('#upload-demo').croppie({
                viewport: {
                    width: 400,
                    height: 400,
                },
                enableExif: true
            });

            $('#upload').on('change', function() {
                readFile(this);
            });
            $('.upload-result').on('click', function(ev) {
                ev.preventDefault();
                $uploadCrop.croppie('result', {
                    type: 'canvas',
                    size: 'viewport'
                }).then(function(resp) {
                    image = resp;
                });
            });
            var table = $('#crud').DataTable({
                ajax: 'service',
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

            btnSave.click(function(e) {
                e.preventDefault();
                var data = form.serialize()
                if (image != "") {
                    data = data + "&image=" + image;
                }
                console.log(data)
                $.ajax({
                    type: "POST",
                    url: "{{ route('service.store') }}",
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


                modal.find('.card-title').text('Modificar')
                modal.find('.modal-footer button[type="submit"]').text('Update')

                let rowData = table.row($(this).parents('tr')).data()

                form.find('input[name="id"]').val(rowData.id)
                form.find('input[name="code"]').val(rowData.code)
                form.find('input[name="name"]').val(rowData.name)
                form.find('textarea[name="description"]').val(rowData.description)

                $(".upload-demo-wrap").hide();
                $(".upload-msg").show();

                if (rowData.last_photo != null) {
                    $(".upload-demo-wrap").show();
                    $(".upload-msg").hide();
                    $('.upload-demo').addClass('ready');
                    $uploadCrop.croppie('bind', {
                        url: rowData.last_photo.path
                    })
                }

                modal.modal()
            })

            btnUpdate.click(function() {
                var formData = form.serialize() + '&_method=PUT&_token=' + token
                if (image != "") {
                    formData = formData + "&image=" + image;
                }
                var updateId = form.find('input[name="id"]').val()
                $.ajax({
                    type: "POST",
                    url: "/service/" + updateId,
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
                            url: "/service/" + rowid,
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

            function add() {
                modal.modal()
                form.trigger('reset')
                modal.find('.card-title').text('Agregar Nuevo')
                btnSave.show();
                btnUpdate.hide()
            }

        })
    </script>
@stop
