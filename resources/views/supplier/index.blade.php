@extends('adminlte::page')

@section('title', 'Proveedores')

@section('content_header')
    &nbsp;
@endsection

@section('content')
    <div class="row justify-content-md-center">
        <div class="col">
            <div class="card card-secondary">
                <div class="card-header sidebar-dark-primary">
                    <h2 class="card-title">Proveedores</h2>
                    <div class="text-right">
                        <button class="btn btn-dt add"><i class="fa fa-plus"></i> Agregar Proveedor</button>
                    </div>
                </div>
                <div class="card-body pb-1 mt-2">
                    <table id="crud" class="table table-bordered table-head-fixed table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Identificación</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Telefono</th>
                                <th>Email</th>
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
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key={{ env('GOOGLE_MAPS_API_KEY') }}">
    </script>

    <script>
        $(document).ready(function() {
            $.noConflict();

            const addressInput = document.getElementById("address");
            let addressLatitude = document.getElementById("addressLatitude");
            let addressLongitude = document.getElementById("addressLongitude");

            autocomplete = new google.maps.places.Autocomplete(addressInput, {
                types: ['geocode'],
            });

            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var near_place = autocomplete.getPlace();
                $("#addressLatitude").val(near_place.geometry.location.lat());
                $("#addressLongitude").val(near_place.geometry.location.lng());
            });

            var token = $('meta[name="csrf-token"]').attr('content');
            var modal = $('.modal');
            var form = $('.form');
            var btnAdd = $('.add'),
                btnSave = $('.btn-save'),
                btnUpdate = $('.btn-update');

            var table = $('#crud').DataTable({
                ajax: 'supplier',
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
                        data: 'identification',
                        name: 'identification'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'last_address.address',
                        name: 'last_address.address'
                    },
                    {
                        data: 'last_phone.phone',
                        name: 'last_phone.phone'
                    },
                    {
                        data: 'last_email.email',
                        name: 'last_email.email'
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
                console.log(data)
                $.ajax({
                    type: "POST",
                    url: "{{ route('supplier.store') }}",
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

                var rowData = table.row($(this).parents('tr')).data()

                form.find('input[name="id"]').val(rowData.id)
                form.find('input[name="name"]').val(rowData.name)
                form.find('input[name="identification"]').val(rowData.identification)
                form.find('select[name="type"]').val(rowData.type)

                form.find('input[name="address"]').val(rowData.last_address.address)
                form.find('input[name="addressLongitude"]').val(rowData.last_address.longitude)
                form.find('input[name="addressLatitude"]').val(rowData.last_address.latitude)

                form.find('input[name="phone"]').val(rowData.last_phone.phone)
                form.find('input[name="email"]').val(rowData.last_email.email)

                modal.modal()
            })

            btnUpdate.click(function() {
                var formData = form.serialize() + '&_method=PUT&_token=' + token
                var updateId = form.find('input[name="id"]').val()
                $.ajax({
                    type: "POST",
                    url: "/supplier/" + updateId,
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
                            url: "/supplier/" + rowid,
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


            $(document).on('change', '#type', function() {
                if ($("#type").val() == 1) {
                    $("#lblName").html("Nombre");
                } else {
                    $("#lblName").html("Razón Social");
                }
            })
        })
    </script>
@stop
