@extends('adminlte::page')

@section('title', 'Categorias')

@section('content_header') &nbsp; @endsection

@section('css')
  {{-- Croppie --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" integrity="sha512-GiP7f2qG7Qm3iKjAoqU9b3e6mN4c0y3G+5qQO+v7x5N9wXz4x7K8i6l8r5gH0h5pMNVjX5C2z8lqijB2M5bZ4w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    .swal2-container{ z-index:3000; }
    .upload-demo-wrap{ display:none; }
    .upload-msg{ text-align:center;padding:16px;border:1px dashed #aaa;color:#777; }
  </style>
@endsection

@section('content')
<div class="row justify-content-md-center">
  <div class="col-12">
    <div class="card card-secondary">
      <div class="card-header sidebar-dark-primary">
        <h2 class="card-title">Categorias</h2>
        <div class="text-right">
            <button class="btn btn-dt add"><i class="fa fa-plus"></i> Agregar Categoria</button>
        </div>
      </div>
      <div class="card-body pb-1 mt-2">
        <table id="crud" class="table table-bordered table-head-fixed table-hover">
          <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Imagen</th>
            <th style="width: 20%">Opciones</th>
          </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Modal (evitar que se cierre afuera si quieres): data-backdrop="static" data-keyboard="false" --}}
<div class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
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
                  <div class="upload-msg">Subir imagen para comenzar a cortar</div>
                  <div class="upload-demo-wrap"><div id="upload-demo"></div></div>
                  <div class="mt-2 d-flex gap-2">
                    <label class="btn btn-sm bg-gradient-dark text-white file-btn mb-0">
                      <span>Nueva Imagen</span>
                      <input type="file" id="upload" accept="image/*" />
                    </label>
                    <button type="button" class="btn btn-sm bg-gradient-dark text-white upload-result">Guardar recorte</button>
                  </div>
                  <hr>
                </div>
                <div class="col">
                  <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" class="form-control form-control-sm input-sm">
                  </div>
                  <div class="form-group">
                    <label for="description">Descripción</label>
                    <input type="text" name="description" class="form-control form-control-sm input-sm">
                  </div>
                </div>
              </div>
            </div>
          </div> {{-- card --}}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-dark text-white btn-save">Guardar</button>
          <button type="button" class="btn bg-gradient-dark text-white btn-update">Modificar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('js')
  {{-- Croppie --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js" integrity="sha512-1bQ2o8n0HhD8ne0m6k5eG9sOQb6xq6ZCIV7gqfQFH2m6K3f5aH3JpC2o8a5H3m2S1Zr6Oqg5Y9P5oJt7v0n3eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
  $(function() {
    var token   = $('meta[name="csrf-token"]').attr('content');
    var modal   = $('.modal');
    var form    = $('.form');
    var btnAdd  = $('.add'),
        btnSave = $('.btn-save'),
        btnUpd  = $('.btn-update');

    let imageBase64 = "";
    let $uploadCrop = $('#upload-demo').croppie({
      viewport: { width: 400, height: 400 },
      enableExif: true
    });

    function readFile(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          $('.upload-demo').addClass('ready');
          $uploadCrop.croppie('bind', { url: e.target.result }).then(function(){});
          $(".upload-demo-wrap").show();
          $(".upload-msg").hide();
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    $('#upload').on('change', function(){ readFile(this); });

    $('.upload-result').on('click', function(ev){
      ev.preventDefault();
      $uploadCrop.croppie('result', { type: 'canvas', size: 'viewport', format: 'jpeg', quality: 0.9 })
        .then(function(resp){ imageBase64 = resp; });
    });

    var table = initCrudTable('#crud', {
      ajax: 'category',
      columns: [
        { data:'id', name:'id' },
        { data:'name', name:'name' },
        { data:'description', name:'description' },
        {
          data: 'last_photo.url',
          orderable: false,
          render: function(data, type, row){
            var src = data || '{{ asset('img/no-image.jpg') }}';
            return '<img src="'+src+'" width="80" height="80" style="object-fit:cover;border-radius:6px;">';
          }
        },
        {
          data: null,
          orderable:false,
          render: function(data,type,row){
            let html = '<div class="form-group mb-0">';
            html += '<a class="btn-edit" title="Modificar" href="#"><button type="button" class="btn btn-sm bg-gradient-dark text-white">Modificar</button></a>&nbsp;';
            html += '<button type="button" class="btn btn-sm bg-gradient-dark text-white btn-delete" data-rowid="'+row.id+'">Eliminar</button>';
            html += '</div>';
            return html;
          }
        },
      ],
    });

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });

    btnAdd.click(function(){ add(); });

    btnSave.click(function(e){
      e.preventDefault();
      var data = form.serialize();
      if (imageBase64) data += '&image='+encodeURIComponent(imageBase64);
      $.post("{{ route('category.store') }}", data, function(res){
        if (res.status==200){ table.draw(); form.trigger("reset"); modal.modal('hide'); }
        else { showErrors(res); }
      }).fail(()=>errorToast("Error al Grabar"));
    });

    $(document).on('click','.btn-edit', function(){
      btnSave.hide(); btnUpd.show();
      modal.find('.card-title').text('Modificar');

      var row = table.row($(this).parents('tr')).data();
      form.find('input[name="id"]').val(row.id);
      form.find('input[name="name"]').val(row.name);
      form.find('input[name="description"]').val(row.description);

      // Reset crop
      imageBase64 = "";
      $(".upload-demo-wrap").hide(); $(".upload-msg").show();
      if (row.last_photo && row.last_photo.url) {
        $(".upload-demo-wrap").show(); $(".upload-msg").hide();
        $uploadCrop.croppie('bind', { url: row.last_photo.url });
      }

      modal.modal();
    });

    btnUpd.click(function(){
      var id = form.find('input[name="id"]').val();
      var data = form.serialize() + '&_method=PUT';
      if (imageBase64) data += '&image='+encodeURIComponent(imageBase64);
      $.post('/category/'+id, data, function(res){
        if (res.status==200){ table.draw(); modal.modal('hide'); }
        else { showErrors(res); }
      }).fail(()=>errorToast("Error al Grabar"));
    });

    $(document).on('click','.btn-delete', function(){
      var rowid = $(this).data('rowid');
      var el = $(this);
      if (!rowid) return;
      Swal.fire({
        title:"Esta seguro de eliminar el registro?",
        icon:"warning", showCancelButton:true, confirmButtonText:"Si", cancelButtonText:"No", showCloseButton:true
      }).then(function(result){
        if (result.value) {
          $.ajax({
            type:"POST", dataType:'JSON', url:'/category/'+rowid,
            data:{ _method:'delete', _token: token },
            success:function(data){ if (data.status==200) table.row(el.parents('tr')).remove().draw(); }
          });
        }
      });
    });

    function add(){
      modal.modal();
      form.trigger('reset');
      modal.find('.card-title').text('Agregar Nuevo');
      btnSave.show(); btnUpd.hide();
      imageBase64 = "";
      $(".upload-demo-wrap").hide();
      $(".upload-msg").show();
    }

    function showErrors(res){
      var error = '';
      if (res.errors) {
        $.each(res.errors, function(k, v){
          if (Array.isArray(v)) error += v.join('<br>')+'<br>'; else error += v+'<br>';
        });
      }
      Swal.fire({ icon:'error', title:'Error', html: error || 'Error al procesar' });
    }
    function errorToast(msg){ Swal.fire({ icon:'error', title:'Error', html: msg }); }
  });
  </script>
@endsection
