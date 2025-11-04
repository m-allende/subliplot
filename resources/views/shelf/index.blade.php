@extends('adminlte::page')

@section('title', 'Estantes')

@section('content_header')
    &nbsp;
@endsection

@section('css')
    <style>
        /* arreglo para mostrar autocomplete en modal! */
        .pac-container {
            z-index: 1061 !important;
        }

        .swal2-container {
            z-index: 3000;
        }
        .shelf-container {
            padding: 20px;
        }
        .shelf {
            margin-top: 20px;
        }
        .shelf-bar {
            background: linear-gradient(135deg, #707070, #8b8b8b);
            height: 10px; 
            border-radius: 5px;
            margin-top: 0; 
        }
        .shelf-row {
            display: flex;
            gap: 5px;
            margin-bottom: 0;
            margin-left: 10px;
            margin-right: 10px;
        }
        .cell-border {
            background: linear-gradient(135deg, #707070, #8b8b8b);
            flex: 0.2;
            height: 50px;
        }
        .cell-last {
            flex: 5;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cell {
            background-color: #dcdcdc;
            border: 2px solid #aaa;
            border-radius: 8px;
            flex: 1;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .cell:hover {
            background-color: #b3b3b3;
        }
        .cell.selected {
            background-color: #98e98b;
            border-color: #3c763d;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            border-radius: 5px;
            padding: 10px 20px;
        }
        .legs {
            display: flex;
            justify-content: space-between;
            margin-top: 0;
        }
        .leg {
            background-color: #3e3e3e;
            width: 20px;
            height: 50px;
            border-radius: 5px;
        }
        .column-labels {
            display: flex;
            justify-content: space-evenly;
            margin-bottom: 5px;
        }
        .column-label {
            width: 100%;
            text-align: center;
            font-size: 16px;
            margin-bottom: 5px;
        }
    </style>
@endsection


@section('content')
    <div class="row justify-content-md-center">
        <div class="col">
            <div class="card card-secondary">
                <div class="card-header sidebar-dark-primary">
                    <h2 class="card-title">Estantes</h2>
                    <div class="text-right">
                        <button class="btn btn-dt add"><i class="fa fa-plus"></i> Agregar Estante</button>
                    </div>
                </div>
                <div class="card-body pb-1 mt-2">
                    <table id="crud" class="table table-bordered table-head-fixed table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Filas</th>
                                <th>Columnas</th>
                                <th>Orden</th>
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
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="row">Filas</label>
                                            <input type="text" name="row" id="row"
                                                class="form-control form-control-sm input-sm">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="col">Columnas</label>
                                            <input type="text" name="col" id="col"
                                                class="form-control form-control-sm input-sm">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="name">Nombre</label>
                                            <input type="text" name="name" id="name"
                                                class="form-control form-control-sm input-sm">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="start">Letra de Inicio</label>
                                            <select class="form-control form-control-sm input-sm" name="start" id="start">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="order">Orden</label>
                                            <input type="text" name="order" id="order"
                                                class="form-control form-control-sm input-sm">
                                        </div>
                                    </div>
                                    <div class="col-12 text-right">
                                        <button type="button" class="btn btn-primary" id="generateShelfInModal">Generar Estante</button>
                                    </div>
                                </div>
                                <div class="mt-4 shelf-container" id="shelfContainerInModal"></div>
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
                ajax: 'shelf',
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'row',
                        name: 'row'
                    },
                    {
                        data: 'col',
                        name: 'col'
                    },
                    {
                        data: 'order',
                        name: 'order'
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
                    url: "{{ route('product.store') }}",
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

                let newOption = new Option(rowData.brand.name, rowData.brand.id, true, true);
                $('#brand_id').append(newOption).trigger('change');

                newOption = new Option(rowData.category.name, rowData.category.id, true, true);
                $('#category_id').append(newOption).trigger('change');

                newOption = new Option(rowData.presentation.name, rowData.presentation.id, true, true);
                $('#presentation_id').append(newOption).trigger('change');

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

                if (rowData.expiration) {
                    var myDate = new Date(rowData.expiration);
                    form.find('input[name="expiration"]').val(myDate.toLocaleDateString('en-GB').replaceAll(
                        "/",
                        "-"));
                }

                modal.modal()
            })

            btnUpdate.click(function() {
                var formData = form.serialize() + '&_method=PUT&_token=' + token
                var updateId = form.find('input[name="id"]').val()
                if (image != "") {
                    formData = formData + "&image=" + image;
                }
                $.ajax({
                    type: "POST",
                    url: "/product/" + updateId,
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
                            url: "/product/" + rowid,
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

            $(".select2-brand").select2({
                placeholder: "Seleccione una Opcion...",
                dropdownParent: $(".modal"),
                escapeMarkup: function(markup) {
                    return markup;
                }, // let our custom formatter work
                ajax: {
                    type: "GET",
                    url: "/brand",
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

            $(".select2-category").select2({
                placeholder: "Seleccione una Opcion...",
                dropdownParent: $(".modal"),
                escapeMarkup: function(markup) {
                    return markup;
                }, // let our custom formatter work
                ajax: {
                    type: "GET",
                    url: "/category",
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

            $(".select2-presentation").select2({
                placeholder: "Seleccione una Opcion...",
                dropdownParent: $(".modal"),
                escapeMarkup: function(markup) {
                    return markup;
                }, // let our custom formatter work
                ajax: {
                    type: "GET",
                    url: "/presentation",
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
            $('#expiration').datetimepicker({
                format: 'DD-MM-yyyy'
            });
        });
    </script>
    <script>
        let shelves = []; // Store shelves data

        // Function to generate column letters (A, B, C, ..., Z, AA, AB, ...)
        function generateColumnLetter(index) {
            let column = '';
            while (index >= 0) {
                column = String.fromCharCode((index % 26) + 65) + column;
                index = Math.floor(index / 26) - 1;
            }
            return column;
        }

        // Initialize the shelf table
        function updateShelfTable() {
            const tableBody = $('#shelfTable tbody');
            tableBody.empty(); // Clear existing rows
            shelves.forEach((shelf, index) => {
                tableBody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${shelf.rows}</td>
                        <td>${shelf.columns}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editShelf(${index})">Editar</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteShelf(${index})">Eliminar</button>
                        </td>
                    </tr>
                `);
            });

            // Reinitialize DataTables after updating the table
            if ($.fn.dataTable.isDataTable('#shelfTable')) {
                $('#shelfTable').DataTable().clear().destroy();
            }
            $('#shelfTable').DataTable();
        }

        // Function to generate the shelf structure inside modal
        function generateShelfInModal(rows, columns) {
            let shelfHtml = '';
            shelfHtml += '<div class="shelf-bar"></div>';
            for (let i = 0; i < rows; i++) {
                shelfHtml += '<div class="shelf-row">';
                shelfHtml += `<div class="cell-border"></div>`;
                for (let j = 0; j < columns; j++) {
                    shelfHtml += '<div class="cell">'+generateColumnLetter(j)+(i+1)+'</div>';
                }
                shelfHtml += `<div class="cell-border"></div>`;
                shelfHtml += '</div>';
                shelfHtml += '<div class="shelf-bar"></div>';
            }
            shelfHtml += '<div class="shelf-row">';
            shelfHtml += `<div class="cell-border"></div>`;
            shelfHtml += `<div class="cell-last"></div>`;
            shelfHtml += `<div class="cell-border"></div>`;
            shelfHtml += '</div>';

            $('#shelfContainerInModal').html(shelfHtml);
        }

        // Handle adding a new shelf
        $('#addShelfBtn').click(function () {
            $('#shelfModalLabel').text('Crear Estante');
            $('#row').val('');
            $('#col').val('');
            $('#shelfContainerInModal').empty();
        });

        // Handle generating shelf inside modal
        $('#generateShelfInModal').click(function () {
            const rows = $('#row').val();
            const columns = $('#col').val();

            if (rows > 0 && columns > 0) {
                generateShelfInModal(rows, columns);
            } else {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Por favor, ingresa valores válidos para filas y columnas.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });

        // Handle saving the new shelf
        $('#saveShelfBtn').click(function () {
            const rows = $('#row').val();
            const columns = $('#col').val();

            if (rows > 0 && columns > 0) {
                shelves.push({ rows: rows, columns: columns });
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Estante creado con éxito.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
                updateShelfTable();
                $('#shelfModal').modal('hide');
            } else {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Por favor, ingresa valores válidos para filas y columnas.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });

        // Function to edit an existing shelf
        function editShelf(index) {
            const shelf = shelves[index];
            $('#shelfModalLabel').text('Editar Estante');
            $('#row').val(shelf.rows);
            $('#col').val(shelf.columns);
            generateShelfInModal(shelf.rows, shelf.columns);
            $('#saveShelfBtn').off('click').click(function () {
                shelves[index] = { rows: $('#row').val(), columns: $('#col').val() };
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Estante actualizado con éxito.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
                $('#shelfModal').modal('hide');
                updateShelfTable();
            });
            $('#shelfModal').modal('show');
        }

        // Function to delete an existing shelf
        function deleteShelf(index) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'No podrás recuperar este estante después de eliminarlo.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    shelves.splice(index, 1); // Delete shelf
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'El estante ha sido eliminado.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                    updateShelfTable();
                }
            });
        }

        $(document).ready(function () {
            updateShelfTable(); // Initialize shelf table when page loads
        });
    </script>
@stop
