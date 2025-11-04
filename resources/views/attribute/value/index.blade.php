@extends('adminlte::page')

@section('title','Valores de Atributo')
@section('content_header') &nbsp; @endsection

@section('css')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endsection

@section('content')
<div class="row justify-content-md-center">
  <div class="col-12">
    <div class="card card-secondary">
      <div class="card-header sidebar-dark-primary">
        <h2 class="card-title mb-0">Valores de Atributo</h2>
        <div class="text-right">
            <button class="btn btn-dt add"><i class="fa fa-plus"></i> Agregar Valor</button>
        </div>
      </div>
      <div class="card-body pb-1 mt-2">
        <table id="crud" class="table table-bordered table-head-fixed table-hover">
          <thead>
          <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Nombre</th>
            <th>Código</th>
            <th>W×H (mm)</th>
            <th>GSM</th>
            <th>Activo</th>
            <th style="width:20%">Opciones</th>
          </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Modal --}}
<div class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <form class="form" action="" method="POST" autocomplete="off">
      <div class="modal-content">
        <div class="modal-body">
          <input type="hidden" name="id">
          <div class="card card-secondary m-2">
            <div class="card-header sidebar-dark-primary">
              <h2 class="card-title">Datos del Valor</h2>
            </div>
            <div class="card-body card-body-gray">
              <div class="form-group">
                <label>Tipo</label>
                <select id="attribute_type_id" name="attribute_type_id" class="form-control select2" style="width:100%"></select>
              </div>
              <div class="form-group">
                <label>Nombre (visible)</label>
                <input type="text" name="name" class="form-control form-control-sm">
              </div>
              <div class="form-group">
                <label>Código (opcional)</label>
                <input type="text" name="code" class="form-control form-control-sm">
              </div>
              <div class="row">
                <div class="col">
                  <label>Width (mm)</label>
                  <input type="number" name="width_mm" class="form-control form-control-sm" min="0">
                </div>
                <div class="col">
                  <label>Height (mm)</label>
                  <input type="number" name="height_mm" class="form-control form-control-sm" min="0">
                </div>
                <div class="col">
                  <label>GSM</label>
                  <input type="number" name="weight_gsm" class="form-control form-control-sm" min="0">
                </div>
              </div>
              <div class="form-group mt-2">
                <label>Color HEX (opcional)</label>
                <input type="text" name="color_hex" class="form-control form-control-sm" placeholder="#FFFFFF">
              </div>
              <div class="form-group">
                <label>Orden</label>
                <input type="number" name="sort_order" class="form-control form-control-sm" value="0">
              </div>
              <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="active" name="active" checked>
                <label class="custom-control-label" for="active">Activo</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-dark btn-save">Guardar</button>
          <button type="button" class="btn bg-gradient-dark btn-update">Modificar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<script>
$(function(){
  var token = $('meta[name="csrf-token"]').attr('content');
  var modal = $('.modal'), form = $('.form');
  var btnAdd=$('.add'), btnSave=$('.btn-save'), btnUpd=$('.btn-update');

  var table = initCrudTable('#crud', {
    ajax: 'attribute-value',
    columns: [
      {data:'id'},
      {data:'type.name', defaultContent:'-'},
      {data:'name'},
      {data:'code', defaultContent:'-'},
      {data:null, render:(r)=> (r.width_mm||0)+'×'+(r.height_mm||0)},
      {data:'weight_gsm', defaultContent:'-'},
      {data:'active', render:(d)=> d?'<span class="badge badge-success">Sí</span>':'<span class="badge badge-secondary">No</span>'},
      {data:null, orderable:false, render:(row)=>{
        return `
          <div class="form-group mb-0">
            <a class="btn-edit" href="#"><button type="button" class="btn btn-sm bg-gradient-dark">Modificar</button></a>&nbsp;
            <button type="button" class="btn btn-sm bg-gradient-dark btn-delete" data-rowid="${row.id}">Eliminar</button>
          </div>`;
      }}
    ],
  });

  $.ajaxSetup({ headers: {'X-CSRF-TOKEN': token} });

  // Select2 tipos (dropdown dentro del modal)
  $('#attribute_type_id').select2({
    placeholder: "Seleccione tipo...",
    dropdownParent: $('.modal'),
    width: '100%',
    ajax: {
      type: "GET",
      url: "/attribute-type/options",
      data: params => ({ search: params.term }),
      processResults: data => ({ results: data.data })
    }
  });

  btnAdd.click(function(){ add(); });
  function add(){
    modal.modal(); form.trigger('reset');
    $('#attribute_type_id').val(null).trigger('change');
    form.find('#active').prop('checked', true);
    btnSave.show(); btnUpd.hide();
  }

  btnSave.click(function(e){
    e.preventDefault();
    $.post("{{ route('attribute-value.store') }}", form.serialize(), function(res){
      if(res.status==200){ table.draw(); form.trigger('reset'); modal.modal('hide'); }
      else showErrors(res);
    }).fail(()=>errorToast('Error al grabar'));
  });

  $(document).on('click','.btn-edit', function(){
    btnSave.hide(); btnUpd.show();
    const row = table.row($(this).parents('tr')).data();

    form.find('input[name="id"]').val(row.id);
    $('#attribute_type_id').empty();
    if(row.type){
      $('#attribute_type_id').append(new Option(row.type.name, row.type.id, true, true)).trigger('change');
    }

    form.find('input[name="name"]').val(row.name);
    form.find('input[name="code"]').val(row.code||'');
    form.find('input[name="width_mm"]').val(row.width_mm||'');
    form.find('input[name="height_mm"]').val(row.height_mm||'');
    form.find('input[name="weight_gsm"]').val(row.weight_gsm||'');
    form.find('input[name="color_hex"]').val(row.color_hex||'');
    form.find('input[name="sort_order"]').val(row.sort_order ?? 0);
    form.find('#active').prop('checked', !!row.active);

    modal.modal();
  });

  btnUpd.click(function(){
    const id = form.find('input[name="id"]').val();
    const data = form.serialize() + '&_method=PUT';
    $.post('/attribute-value/'+id, data, function(res){
      if(res.status==200){ table.draw(); modal.modal('hide'); }
      else showErrors(res);
    }).fail(()=>errorToast('Error al grabar'));
  });

  $(document).on('click','.btn-delete', function(){
    const id=$(this).data('rowid'); const el=$(this);
    Swal.fire({title:"¿Eliminar registro?",icon:"warning",showCancelButton:true,confirmButtonText:"Si",cancelButtonText:"No"})
    .then(r=>{
      if(r.value){
        $.post('/attribute-value/'+id, {_method:'delete', _token:token}, function(res){
          if(res.status==200) table.row(el.parents('tr')).remove().draw();
        });
      }
    });
  });

  function showErrors(res){
    let html=''; if(res.errors){ $.each(res.errors,(k,v)=> html += (Array.isArray(v)?v.join('<br>'):v)+'<br>'); }
    Swal.fire({icon:'error',title:'Error',html:html||'Error al procesar'});
  }
  function errorToast(m){ Swal.fire({icon:'error',title:'Error',html:m}); }
});
</script>
@endsection
