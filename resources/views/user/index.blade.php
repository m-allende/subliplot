@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    &nbsp;
@endsection

@section('content')
<div class="row justify-content-md-center">
  <div class="col-12">
    <div class="card card-secondary">
      <div class="card-header sidebar-dark-primary">
        <h2 class="card-title">Usuarios</h2>
        <div class="text-right">
          <button class="btn btn-dt add"><i class="fa fa-plus"></i> Agregar Usuario</button>
        </div>
      </div>
      <div class="card-body pb-1 mt-2">
        <table id="crud" class="table table-bordered table-head-fixed table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>RUT</th>
              <th style="width: 20%">Opciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Modal --}}
<div class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <form class="form" action="" method="POST" autocomplete="off" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-body">
          <input type="hidden" name="id">
          <div class="card card-secondary m-2">
            <div class="card-header sidebar-dark-primary">
              <h2 class="card-title">Datos Generales</h2>
            </div>
            <div class="card-body card-body-gray">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="role_id">Rol</label>
                    <select id="role_id" name="role_id"
                      class="form-control select2 select2-danger select2-role"
                      data-dropdown-css-class="select2-danger" style="width:100%;"></select>
                  </div>

                  <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" class="form-control form-control-sm input-sm">
                  </div>

                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" class="form-control form-control-sm input-sm">
                  </div>

                  <div class="form-group">
                    <label for="rut">RUT</label>
                    <input type="text" name="rut" class="form-control form-control-sm input-sm" placeholder="12.345.678-9">
                  </div>

                  <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" class="form-control form-control-sm input-sm">
                  </div>

                  <hr class="my-3">

                  <div class="row">
                    <div class="col-md-6">
                      <label>País</label>
                      <select id="country_id" name="country_id" class="form-control select2" style="width:100%"></select>
                    </div>
                    <div class="col-md-6">
                      <label>Región</label>
                      <select id="region_id" name="region_id" class="form-control select2" style="width:100%"></select>
                    </div>
                  </div>

                  <div class="row mt-2">
                    <div class="col-md-6">
                      <label>Comuna</label>
                      <select id="commune_id" name="commune_id" class="form-control select2" style="width:100%"></select>
                    </div>
                    <div class="col-md-6">
                      <label>Código Postal</label>
                      <input type="text" name="addr_postal" class="form-control form-control-sm input-sm">
                    </div>
                  </div>

                  <div class="form-group mt-2">
                    <label>Dirección</label>
                    <input type="text" name="addr_line1" class="form-control form-control-sm input-sm" placeholder="Calle y número">
                  </div>
                  <div class="form-group">
                    <label>Depto/Oficina</label>
                    <input type="text" name="addr_line2" class="form-control form-control-sm input-sm">
                  </div>
                  <div class="form-group">
                    <label>Referencia</label>
                    <input type="text" name="addr_reference" class="form-control form-control-sm input-sm">
                  </div>

                  <div class="form-group">
                    <label>Teléfono</label>
                    <div class="input-group input-group-sm">
                      <input type="text" name="phone_country" class="form-control" value="+56" style="max-width:80px">
                      <input type="text" name="phone_number" class="form-control" placeholder="9 1234 5678">
                    </div>
                    <input type="hidden" name="phone_kind" value="mobile">
                  </div>
                </div>

                {{-- Columna Avatar con Crop --}}
                <div class="col-md-4">
                  <label>Avatar</label>
                  <div class="border rounded p-2 text-center">
                    <div class="mb-2">
                      <img id="avatarPreview" src="https://www.gravatar.com/avatar/?s=160&d=identicon"
                           style="max-width:100%; max-height:280px;" alt="Avatar preview">
                    </div>
                    <div class="d-grid gap-2">
                      <input type="file" id="avatarInput" accept="image/*" class="form-control form-control-sm">
                      <button type="button" class="btn btn-sm btn-outline-primary" id="btnCrop">Aplicar recorte</button>
                      <small class="text-muted">Formatos: JPG/PNG/WEBP · Máx 3MB</small>
                    </div>
                  </div>
                  {{-- Campo oculto para Blob recortado (se adjunta al FormData dinámicamente) --}}
                </div>

              </div>{{-- row --}}
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

@section('css')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  {{-- Cropper.js --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
@endsection

@section('js')
  <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
  {{-- Cropper.js --}}
  <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

  <script>
  $(document).ready(function() {
    $.noConflict();

    var token   = $('meta[name="csrf-token"]').attr('content');
    var modal   = $('.modal');
    var form    = $('.form');
    var btnAdd  = $('.add'),
        btnSave = $('.btn-save'),
        btnUpd  = $('.btn-update');

    const $dropdownParent = $(".modal-body");

    // DataTable
    var table = $('#crud').DataTable({
      ajax: 'user',
      serverSide: true,
      processing: true,
      aaSorting: [[0, "asc"]],
      language: { url: "{{ asset('json/datatable-ES.json') }}" },
      dom: 'Bftirp',
      columns: [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'rut', name: 'rut' },
        {
          data: 'action',
          orderable: false,
          render: function(data, type, row) {
            let html = '<div class="form-group">';
            html += '<a class="btn-edit" title="Modificar" href="#"><button type="button" class="btn btn-sm bg-gradient-dark">Modificar</button></a>&nbsp;';
            html += '<button type="button" class="btn btn-sm bg-gradient-dark btn-delete" data-rowid="'+row.id+'">Eliminar</button>';
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
          didOpen: function() { Swal.showLoading() }
        });
      } else {
        Swal.close();
      }
    });

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });

    // SELECT2: Roles
    $(".select2-role").select2({
      placeholder: "Seleccione Rol...",
      dropdownParent: $dropdownParent,
      width: '100%',
      ajax: {
        type: "GET",
        url: "/role",
        data: params => ({ search: params.term }),
        processResults: data => ({ results: data.data })
      },
      templateResult: d => d.loading ? d.text : $('<div>'+ d.name +'</div>'),
      templateSelection: d => d.name || d.text,
    });

    // SELECT2: País / Región / Comuna (dependientes)
    $('#country_id').select2({
      placeholder: "Seleccione País...",
      dropdownParent: $dropdownParent,
      width: '100%',
      ajax: {
        type: "GET",
        url: "/country",
        data: params => ({ search: params.term }),
        processResults: data => ({ results: data.data })
      },
      templateResult: d => d.loading ? d.text : $('<div>'+ d.name +'</div>'),
      templateSelection: d => d.name || d.text,
    });

    $('#region_id').select2({
      placeholder: "Seleccione Región...",
      dropdownParent: $dropdownParent,
      width: '100%',
      ajax: {
        type: "GET",
        url: "/region",
        data: params => ({ search: params.term, country_id: $('#country_id').val() }),
        processResults: data => ({ results: data.data })
      },
      templateResult: d => d.loading ? d.text : $('<div>'+ d.name +'</div>'),
      templateSelection: d => d.name || d.text,
    });

    $('#commune_id').select2({
      placeholder: "Seleccione Comuna...",
      dropdownParent: $dropdownParent,
      width: '100%',
      ajax: {
        type: "GET",
        url: "/commune",
        data: params => ({ search: params.term, region_id: $('#region_id').val() }),
        processResults: data => ({ results: data.data })
      },
      templateResult: d => d.loading ? d.text : $('<div>'+ d.name +'</div>'),
      templateSelection: d => d.name || d.text,
    });

    $('#country_id').on('change', function(){
      $('#region_id').val(null).trigger('change');
      $('#commune_id').val(null).trigger('change');
    });
    $('#region_id').on('change', function(){
      $('#commune_id').val(null).trigger('change');
    });

    $dropdownParent.on('shown.bs.modal', function () {
        $('#role_id, #country_id, #region_id, #commune_id').each(function(){
            const $el = $(this);
            if ($el.data('select2')) {
            $el.select2('close'); // evita que quede abierto con mal cálculo
            }
        });
    });

    // CROP: Avatar
    let cropper = null, avatarBlob = null;
    const avatarImg   = document.getElementById('avatarPreview');
    const avatarInput = document.getElementById('avatarInput');
    const btnCrop     = document.getElementById('btnCrop');

    avatarInput.addEventListener('change', function(e){
      const file = e.target.files?.[0];
      if (!file) return;

      const url = URL.createObjectURL(file);
      avatarImg.src = url;

      // Destruir cropper previo
      if (cropper) { cropper.destroy(); cropper = null; }

      cropper = new Cropper(avatarImg, {
        aspectRatio: 1,
        viewMode: 1,
        movable: false,
        zoomable: true,
        rotatable: false,
        scalable: false,
        responsive: true,
        dragMode: 'move',
        minContainerWidth: 260,
        minContainerHeight: 260,
      });
    });

    btnCrop.addEventListener('click', function(){
      if (!cropper) return;
      cropper.getCroppedCanvas({ width: 512, height: 512 })
        .toBlob(function(blob){
          avatarBlob = blob; // lo adjuntamos al enviar
          // Vista previa con el recorte final
          const url = URL.createObjectURL(blob);
          avatarImg.src = url;
          cropper.destroy(); cropper = null;
        }, 'image/webp', 0.92);
    });

    // Abrir modal (nuevo)
    btnSave.show(); btnUpd.hide();
    $('.add').click(function() {
      add();
    });

    function add() {
      modal.modal();
      form.trigger('reset');
      $('#role_id, #country_id, #region_id, #commune_id').val(null).trigger('change');
      modal.find('.card-title').text('Agregar Nuevo');
      btnSave.show(); btnUpd.hide();

      // Reset avatar
      avatarBlob = null;
      if (cropper) { cropper.destroy(); cropper = null; }
      $('#avatarPreview').attr('src','https://www.gravatar.com/avatar/?s=160&d=identicon');
      $('#avatarInput').val('');
    }

    // Guardar (CREATE) con FormData
    btnSave.click(async function(e){
      e.preventDefault();
      const fd = new FormData(form[0]);

      // Adjunta avatar recortado si existe
      if (avatarBlob) {
        fd.append('avatar', avatarBlob, 'avatar.webp');
      } else if ($('#avatarInput')[0].files[0]) {
        // si no se recortó, sube tal cual
        fd.append('avatar', $('#avatarInput')[0].files[0]);
      }

      try {
        const res = await $.ajax({
          url: "{{ route('user.store') }}",
          method: "POST",
          data: fd,
          cache: false,
          processData: false,
          contentType: false,
        });
        if (res.status == 200) {
          table.draw();
          form.trigger("reset");
          modal.modal('hide');
        } else {
          showErrors(res);
        }
      } catch (err) {
        errorToast("Error al Grabar");
      }
    });

    // Editar
    $(document).on('click', '.btn-edit', function() {
      btnSave.hide(); btnUpd.show();

      modal.find('.card-title').text('Modificar');

      const row = table.row($(this).parents('tr')).data();

      form.find('input[name="id"]').val(row.id);
      form.find('input[name="name"]').val(row.name);
      form.find('input[name="email"]').val(row.email);
      form.find('input[name="rut"]').val(row.rut || '');
      form.find('input[name="password"]').val('');

      // Rol (primer rol si existe)
      if (row.roles && row.roles.length > 0) {
        let opt = new Option(row.roles[0].name, row.roles[0].id, true, true);
        $('#role_id').append(opt).trigger('change');
      } else {
        $('#role_id').val(null).trigger('change');
      }

      // Direccion/Geo (si la traes en el JSON)
      const addr = (row.addresses||[]).find(a=>a.is_primary) || null;
      if (addr){
        if (addr.country_id){
          $('#country_id').append(new Option(addr.country?.name || '(País)', addr.country_id, true, true)).trigger('change');
        } else { $('#country_id').val(null).trigger('change'); }
        if (addr.region_id){
          $('#region_id').append(new Option(addr.region?.name || '(Región)', addr.region_id, true, true)).trigger('change');
        } else { $('#region_id').val(null).trigger('change'); }
        if (addr.commune_id){
          $('#commune_id').append(new Option(addr.commune?.name || '(Comuna)', addr.commune_id, true, true)).trigger('change');
        } else { $('#commune_id').val(null).trigger('change'); }
        $('input[name="addr_line1"]').val(addr.line1 || '');
        $('input[name="addr_line2"]').val(addr.line2 || '');
        $('input[name="addr_reference"]').val(addr.reference || '');
        $('input[name="addr_postal"]').val(addr.postal_code || '');
      } else {
        $('#country_id, #region_id, #commune_id').val(null).trigger('change');
        $('input[name="addr_line1"],[name="addr_line2"],[name="addr_reference"],[name="addr_postal"]').val('');
      }

      // Teléfono
      const ph = (row.phones||[]).find(p=>p.is_default) || null;
      $('input[name="phone_country"]').val(ph?.country_code || '+56');
      $('input[name="phone_number"]').val(ph?.number || '');

      // Avatar (si tienes url en JSON de photo primaria, úsala)
      const photo = (row.photos||[]).find(p=>p.is_primary) || null;
      $('#avatarPreview').attr('src', photo ? (photo.url || photo.path) : 'https://www.gravatar.com/avatar/?s=160&d=identicon');
      avatarBlob = null; if (cropper) { cropper.destroy(); cropper = null; }
      $('#avatarInput').val('');

      modal.modal();
    });

    // Modificar (UPDATE) con FormData
    btnUpd.click(async function(){
      const id = form.find('input[name="id"]').val();
      const fd = new FormData(form[0]);
      fd.append('_method', 'PUT');

      if (avatarBlob) {
        fd.append('avatar', avatarBlob, 'avatar.webp');
      } else if ($('#avatarInput')[0].files[0]) {
        fd.append('avatar', $('#avatarInput')[0].files[0]);
      }

      try {
        const res = await $.ajax({
          url: "/user/" + id,
          method: "POST",
          data: fd,
          cache: false,
          processData: false,
          contentType: false,
        });
        if (res.status == 200) {
          table.draw();
          modal.modal('hide');
        } else {
          showErrors(res);
        }
      } catch (err) {
        errorToast("Error al Grabar");
      }
    });

    // Eliminar
    $(document).on('click', '.btn-delete', function() {
      var rowid = $(this).data('rowid');
      var el = $(this);
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
            url: "/user/" + rowid,
            data: { _method: 'delete', _token: token },
            success: function(data) {
              if (data.status == 200) {
                table.row(el.parents('tr')).remove().draw();
              }
            }
          });
        }
      });
    });

    function showErrors(data){
      var error = '';
      if (data.errors) {
        $.each(data.errors, function(key, err_values) {
          if (Array.isArray(err_values)) error += err_values.join('<br>') + '<br>';
          else error += err_values + '<br>';
        });
      }
      Swal.fire({ icon: 'error', title: "Error", html: error || 'Error al procesar' });
    }

    function errorToast(msg){
      Swal.fire({ icon: 'error', title: "Error", html: msg });
    }
  });
  </script>
@stop
