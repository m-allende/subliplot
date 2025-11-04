@extends('adminlte::page')

@section('title','Tipos de Atributo')
@section('content_header') &nbsp; @endsection

@section('content')
<div class="row justify-content-md-center">
  <div class="col-12">
    <div class="card card-secondary">
      <div class="card-header sidebar-dark-primary">
        <h2 class="card-title mb-0">Tipos de Atributo</h2>
        <div class="text-right">
            <button class="btn btn-dt add"><i class="fa fa-plus"></i> Agregar Tipo</button>
        </div>
      </div>
      <div class="card-body pb-1 mt-2">
        <table id="crud" class="table table-bordered table-head-fixed table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Código</th>
              <th>Nombre</th>
              <th>Activo</th>
              <th>Orden</th>
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
    <form class="form" method="POST" autocomplete="off">
      <div class="modal-content">
        <div class="modal-body">
          <input type="hidden" name="id">
          <div class="card card-secondary m-2">
            <div class="card-header sidebar-dark-primary">
              <h2 class="card-title">Datos del Tipo</h2>
            </div>
            <div class="card-body card-body-gray">
              <div class="form-group">
                <label>Código (interno)</label>
                <input type="text" name="code" class="form-control form-control-sm">
              </div>
              <div class="form-group">
                <label>Nombre (visible)</label>
                <input type="text" name="name" class="form-control form-control-sm">
              </div>
              <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="description" class="form-control form-control-sm">
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
<script>
$(function(){
  var token = $('meta[name="csrf-token"]').attr('content');
  var modal = $('.modal'), form = $('.form');
  var btnAdd=$('.add'), btnSave=$('.btn-save'), btnUpd=$('.btn-update');

  var table = initCrudTable('#crud', {
      ajax: 'attribute-type',
      columns: [
      {data:'id'}, {data:'code'}, {data:'name'},
      {data:'active', render:(d)=> d?'<span class="badge badge-success">Sí</span>':'<span class="badge badge-secondary">No</span>'},
      {data:'sort_order'},
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

  btnAdd.click(function(){ add(); });
  function add(){
    modal.modal(); form.trigger('reset');
    form.find('input[name="id"]').val('');
    form.find('#active').prop('checked', true);
    btnSave.show(); btnUpd.hide();
  }

  btnSave.click(function(e){
    e.preventDefault();
    const data = form.serializeArray();
    $.post("{{ route('attribute-type.store') }}", $.param(data), function(res){
      if(res.status==200){ table.draw(); form.trigger('reset'); modal.modal('hide'); }
      else showErrors(res);
    }).fail(()=>errorToast('Error al grabar'));
  });

  $(document).on('click','.btn-edit', function(){
    btnSave.hide(); btnUpd.show();
    const row = table.row($(this).parents('tr')).data();
    form.find('input[name="id"]').val(row.id);
    form.find('input[name="code"]').val(row.code);
    form.find('input[name="name"]').val(row.name);
    form.find('input[name="description"]').val(row.description||'');
    form.find('input[name="sort_order"]').val(row.sort_order ?? 0);
    form.find('#active').prop('checked', !!row.active);
    modal.modal();
  });

  btnUpd.click(function(){
    const id = form.find('input[name="id"]').val();
    const data = form.serializeArray().concat([{name:'_method', value:'PUT'}]);
    $.post('/attribute-type/'+id, $.param(data), function(res){
      if(res.status==200){ table.draw(); modal.modal('hide'); }
      else showErrors(res);
    }).fail(()=>errorToast('Error al grabar'));
  });

  $(document).on('click','.btn-delete', function(){
    const id=$(this).data('rowid'); const el=$(this);
    Swal.fire({title:"¿Eliminar registro?",icon:"warning",showCancelButton:true,confirmButtonText:"Si",cancelButtonText:"No"})
    .then(r=>{
      if(r.value){
        $.post('/attribute-type/'+id, {_method:'delete', _token:token}, function(res){
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
