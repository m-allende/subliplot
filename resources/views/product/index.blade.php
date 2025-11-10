{{-- resources/views/product/index.blade.php --}}
@extends('adminlte::page')

@section('title','Productos')
@section('content_header') &nbsp; @endsection

@section('css')
  {{-- Croppie --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css"/>
  <style>
    .swal2-container{ z-index:3000; }
    .upload-demo-wrap{ display:none; }
    .upload-msg{ text-align:center;padding:12px;border:1px dashed #aaa;color:#777; }
    .thumb{ width:64px; height:64px; object-fit:cover; border-radius:6px; border:1px solid #ddd; }
    .thumb-wrap{ display:flex; gap:.5rem; flex-wrap:wrap; }
    .croppie-container { width:100%; }
    .cr-boundary { width:100% !important; max-width: 460px; height: 340px !important; } /* <- altura explícita */

  </style>
@endsection

@section('content')
<div class="row justify-content-md-center">
  <div class="col-12">
    <div class="card card-secondary">
      <div class="card-header sidebar-dark-primary">
        <h2 class="card-title">Productos</h2>
        <div class="text-right">
          <button class="btn btn-dt add"><i class="fa fa-plus"></i> Agregar Producto</button>
        </div>
      </div>
      <div class="card-body pb-1 mt-2">
        <table id="crud" class="table table-bordered table-head-fixed table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Categoría</th>
              <th>Act.</th>
              <th>Foto</th>
              <th>Flags</th>
              <th style="width:20%">Opciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Modal CRUD de Producto --}}
<div id="productCrudModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <form id="productCrudForm" class="productCrudForm" method="POST" autocomplete="off">
      @csrf
      <div class="modal-content">
        <div class="modal-body">
          <input type="hidden" name="id">
          <div class="card card-secondary m-2">
            <div class="card-header sidebar-dark-primary">
              <h2 class="card-title m-0">Datos del Producto</h2>
            </div>
            <div class="card-body card-body-gray">
              <div class="row">
                <div class="col-md-7">

                  <div class="form-group">
                    <label>Categoría</label>
                    <select id="category_id" name="category_id" class="form-control select2" style="width:100%"></select>
                  </div>

                  <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="name" class="form-control form-control-sm">
                  </div>

                  <div class="form-group">
                    <label>Subtítulo</label>
                    <input type="text" name="subtitle" class="form-control form-control-sm">
                  </div>

                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="description" class="form-control form-control-sm" rows="3"></textarea>
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="active" name="active" checked>
                      <label class="custom-control-label" for="active">Activo</label>
                    </div>
                  </div>

                  <hr>
                  <label class="mb-2 d-block">Configuraciones disponibles (flags)</label>
                  <div class="row">
                    <div class="col-6">
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_size"        id="uses_size"><label class="form-check-label" for="uses_size">Usa tamaños</label></div>
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_paper"       id="uses_paper"><label class="form-check-label" for="uses_paper">Tipo de papel</label></div>
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_bleed"       id="uses_bleed"><label class="form-check-label" for="uses_bleed">Corte excedente</label></div>
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_finish"      id="uses_finish"><label class="form-check-label" for="uses_finish">Acabados</label></div>
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_print_side"  id="uses_print_side"><label class="form-check-label" for="uses_print_side">1/2 caras</label></div>
                    </div>
                    <div class="col-6">
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_material"    id="uses_material"><label class="form-check-label" for="uses_material">Material</label></div>
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_shape"       id="uses_shape"><label class="form-check-label" for="uses_shape">Forma</label></div>
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_mounting"    id="uses_mounting"><label class="form-check-label" for="uses_mounting">Montaje</label></div>
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_rolling"     id="uses_rolling"><label class="form-check-label" for="uses_rolling">En rollo</label></div>
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_holes"       id="uses_holes"><label class="form-check-label" for="uses_holes">Ojetillos</label></div>
                      <div class="form-check"><input class="form-check-input" type="checkbox" name="uses_quantity" id="uses_quantity"><label class="form-check-label" for="uses_quantity">Cantidad</label></div>
                    </div>
                  </div>

                </div>

                {{-- Columna imágenes (multi) --}}
                <div class="col-md-5">
                  <label>Imágenes (slider)</label>
                  <div class="upload-msg mb-2">Sube una imagen, recórtala y “Agregar a galería”.</div>
                  <div class="upload-demo-wrap"><div id="upload-demo"></div></div>
                  <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="switchReplacePrimary">
                    <label class="form-check-label" for="switchReplacePrimary">Reemplazar foto principal</label>
                  </div>
                  <input type="hidden" name="replace_primary" value="0">
                  <div class="mt-2 d-flex gap-2">
                    <label class="btn btn-sm bg-gradient-dark text-white file-btn mb-0">
                      <span>Nueva Imagen</span>
                      <input type="file" id="upload" accept="image/*"/>
                    </label>
                    <button type="button" class="btn btn-sm bg-gradient-dark text-white add-to-gallery">Agregar a galería</button>
                  </div>
                  <div class="mt-3">
                    <div class="small text-muted mb-1">Galería a guardar (nuevas):</div>
                    <div id="thumbs" class="thumb-wrap"></div>
                  </div>
                  <div class="mt-3">
                    <div class="small text-muted mb-1">Actuales (principal primero):</div>
                    <div id="thumbs-current" class="thumb-wrap"></div>
                  </div>
                  <input type="hidden" name="replace_primary" value="0">
                </div>

              </div>{{-- row --}}
            </div>
          </div>
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

{{-- Modal Asignar Atributos --}}
<div class="modal fade" id="attrLinkModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body">
        <div class="card card-secondary m-2">
            <div class="card-header sidebar-dark-primary">
              <h2 class="card-title m-0">Atributos del producto: <span id="pa_prod_name" class="text-bold"></span></h2>
            </div>
            <div class="card-body card-body-gray">
              <div id="pa_container"><!-- grupos por JS --></div>
            </div>
          </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-dark text-white" id="pa_save">Guardar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
  <script>
  $(function(){
    const token = $('meta[name="csrf-token"]').attr('content');

    // ====== refs modal Producto (IDs y clases CONSISTENTES) ======
    const $productModal = $('#productCrudModal');
    const $productForm  = $('#productCrudForm'); // <form id="productCrudForm">
    const btnAdd  = $('.add');
    const btnSave = $productModal.find('.btn-save');
    const btnUpd  = $productModal.find('.btn-update');

    // ====== Select2 Categoría (dropdown dentro del modal correcto) ======
    const $dp = $productModal.find('.modal-body');
    $('#category_id').select2({
      placeholder: "Seleccione categoría...",
      dropdownParent: $dp,
      width: '100%',
      ajax: {
        type: "GET", url: "/category",
        data: params => ({ search: params.term }),
        processResults: data => ({ results: data.data })
      },
      templateResult: d => d.loading ? d.text : $('<div>'+ d.name +'</div>'),
      templateSelection: d => d.name || d.text,
    });

    // ====== Croppie (multi) ======
    let $crop = null;
    function initCroppie(){
      if ($crop) { $('#upload-demo').croppie('destroy'); $crop = null; }
      $crop = $('#upload-demo').croppie({
        viewport: { width: 380, height: 300 },
        enableExif: true
      });
    }

    function resetCrop(){
      pendingImages = [];
      $('#thumbs').empty();
      $(".upload-demo-wrap").hide(); $(".upload-msg").show();
      if ($crop) { $('#upload-demo').croppie('destroy'); $crop = null; }
    }

    // al mostrar el modal => reinit croppie para que calcule tamaño real
    $productModal.on('shown.bs.modal', function(){
      initCroppie();
    });

    $('#upload').on('change', function(){
      if (!this.files || !this.files[0]) return;
      const reader = new FileReader();
      reader.onload = e => {
        if (!$crop) initCroppie();
        $crop.croppie('bind', { url: e.target.result });
        $(".upload-demo-wrap").show(); $(".upload-msg").hide();
      };
      reader.readAsDataURL(this.files[0]);
    });

    $('.add-to-gallery').on('click', function(){
      if (!$crop) return;
      $crop.croppie('result', { type:'canvas', size:'viewport', format:'jpeg', quality:0.9 })
        .then(function(b64){
          pendingImages.push(b64);
          // thumb con botón X para quitar del “pendiente”
          const idx = pendingImages.length - 1;
          $('#thumbs').append(`
            <div class="position-relative d-inline-block me-1 mb-1">
              <img class="thumb" src="${b64}">
              <button type="button" class="btn btn-xs btn-danger position-absolute" 
                      style="top:-6px; right:-6px; padding:0 .35rem" data-pend="${idx}">×</button>
            </div>
          `);
        });
    });

    // quitar imagen pendiente (antes de guardar)
    $(document).on('click', 'button[data-pend]', function(){
      const i = +$(this).data('pend');
      pendingImages.splice(i,1);
      $(this).parent().remove();
    });

    $('#switchReplacePrimary').on('change', function(){
      $productForm.find('input[name="replace_primary"]').val(this.checked ? '1' : '0');
    });


    // ====== DataTable (usa tu helper initCrudTable; si no existe, inicializa simple) ======
    const dtOptions = {
      ajax: 'product',
      columns: [
        { data:'id', name:'id' },
        { data:'name', name:'name' },
        { data:'category.name', name:'category.name', defaultContent: '-' },
        { data:'active', name:'active', render:(d)=> d ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-secondary">No</span>' },
        {
          data:'primary_photo.url', orderable:false,
          render:(d)=> '<img src="'+(d || "{{ asset('img/no-image.jpg') }}")+'" width="60" height="60" style="object-fit:cover;border-radius:6px;">'
        },
        {
          data:null, orderable:false,
          render:(row)=>{
            const flags = [];
            if (row.uses_size) flags.push('Tamaño');
            if (row.uses_paper) flags.push('Papel');
            if (row.uses_bleed) flags.push('Corte');
            if (row.uses_finish) flags.push('Acabado');
            if (row.uses_material) flags.push('Material');
            if (row.uses_shape) flags.push('Forma');
            if (row.uses_print_side) flags.push('1/2 caras');
            if (row.uses_mounting) flags.push('Montaje');
            if (row.uses_rolling) flags.push('Rollo');
            if (row.uses_holes) flags.push('Ojetillos');
            if (row.uses_quantity) flags.push('Cantidad');
            return '<small>'+ (flags.join(' · ') || '-') +'</small>';
          }
        },
        {
          data:null, orderable:false,
          render:(row)=>{
            let h = '<div class="form-group mb-0">';
            h += '<a class="btn-edit" href="#"><button type="button" class="btn btn-sm bg-gradient-dark text-white">Modificar</button></a>&nbsp;';
            h += '<button type="button" class="btn btn-sm bg-gradient-dark text-white btn-attrs" data-rowid="'+row.id+'" data-rowname="'+(row.name||'')+'">Atributos</button>&nbsp;';
            h += '<button type="button" class="btn btn-sm bg-gradient-dark text-white btn-delete" data-rowid="'+row.id+'">Eliminar</button>';
            h += '</div>';
            return h;
          }
        }
      ],
    };
    const table = (window.initCrudTable ? initCrudTable('#crud', dtOptions) : $('#crud').DataTable(dtOptions));

    $.ajaxSetup({ headers: {'X-CSRF-TOKEN': token} });

    // ====== Nuevo ======
    btnAdd.on('click', function(){
      $productModal.modal('show');
      $productForm.trigger('reset');
      $('#category_id').val(null).trigger('change');
      $productModal.find('.card-title').text('Agregar Nuevo');
      btnSave.show(); btnUpd.hide();
      resetCrop();
      $('#thumbs-current').empty();
      $productForm.find('input[name="replace_primary"]').val('0');
      $productForm.find('#active').prop('checked', true);
    });

    // ====== Guardar (CREATE) ======
    btnSave.on('click', function(e){
      e.preventDefault();
      const data = $productForm.serializeArray();
      pendingImages.forEach(b64 => data.push({name:'images[]', value: b64}));
      $.post("{{ route('product.store') }}", $.param(data), function(res){
        if (res.status==200){ table.ajax.reload(null,false); $productForm.trigger('reset'); $productModal.modal('hide'); }
        else { showErrors(res); }
      }).fail(()=>errorToast("Error al Grabar"));
    });

    // ====== Editar (cargar datos) ======
    $(document).on('click','.btn-edit', function(e){
      e.preventDefault();
      btnSave.hide(); btnUpd.show();
      $productModal.find('.card-title').text('Modificar');
      resetCrop(); $('#thumbs-current').empty();

      const row = table.row($(this).parents('tr')).data();
      $productForm.find('input[name="id"]').val(row.id);
      $productForm.find('input[name="name"]').val(row.name);
      $productForm.find('input[name="subtitle"]').val(row.subtitle || '');
      $productForm.find('textarea[name="description"]').val(row.description || '');
      $productForm.find('#active').prop('checked', !!row.active);

      ['uses_size','uses_paper','uses_bleed','uses_finish','uses_material',
       'uses_shape','uses_print_side','uses_mounting','uses_rolling','uses_holes', 'uses_quantity'
      ].forEach(k => $productForm.find(`[name="${k}"]`).prop('checked', !!row[k]));

      if (row.category){
        const opt = new Option(row.category.name, row.category.id, true, true);
        $('#category_id').append(opt).trigger('change');
      } else {
        $('#category_id').val(null).trigger('change');
      }

      if (row.photos && row.photos.length){
        row.photos.forEach(photo => {
          $('#thumbs-current').append(`
            <div class="position-relative d-inline-block me-1 mb-1">
              <img class="thumb" src="${photo.url}">
              ${photo.is_primary ? '<span class="badge badge-primary position-absolute" style="bottom:-6px;left:0;">Principal</span>' : ''}
              <button type="button" class="btn btn-xs btn-danger position-absolute js-del-photo" 
                      style="top:-6px; right:-6px; padding:0 .35rem"
                      data-photo="${photo.id}" data-product="${row.id}">×</button>
            </div>
          `);
        });
      }


      $productModal.modal('show');
    });

    // ====== Modificar (UPDATE) ======
    btnUpd.on('click', function(){
      const id = $productForm.find('input[name="id"]').val();
      const data = $productForm.serializeArray();
      pendingImages.forEach(b64 => data.push({name:'images[]', value: b64}));
      $.post('/product/'+id, $.param(data.concat([{name:'_method', value:'PUT'}])), function(res){
        if (res.status==200){ table.ajax.reload(null,false); $productModal.modal('hide'); }
        else { showErrors(res); }
      }).fail(()=>errorToast("Error al Grabar"));
    });

    // ====== Eliminar ======
    $(document).on('click','.btn-delete', function(){
      const rowid = $(this).data('rowid'); const el = $(this);
      if (!rowid) return;
      Swal.fire({title:"Esta seguro de eliminar el registro?",icon:"warning",showCancelButton:true,confirmButtonText:"Si",cancelButtonText:"No",showCloseButton:true})
      .then(function(result){
        if (result.value){
          $.ajax({ type:"POST", dataType:'JSON', url:'/product/'+rowid, data:{ _method:'delete', _token: token },
            success:function(data){ if (data.status==200) table.row(el.parents('tr')).remove().draw(); }
          });
        }
      });
    });

    $(document).on('click', '.js-del-photo', async function(){
      const photoId   = $(this).data('photo');
      const productId = $(this).data('product');
      const $wrap = $(this).parent();

      const ok = await Swal.fire({title:'¿Eliminar foto?', icon:'warning', showCancelButton:true});
      if (!ok.isConfirmed) return;

      $.ajax({
        url: `/product/${productId}/photos/${photoId}`,
        type: 'DELETE',
        dataType: 'json',
        headers: {'X-CSRF-TOKEN': token},
        success: function(r){
          if (r.status === 200) {
            $wrap.remove();
            Swal.fire({toast:true, icon:'success', title:'Foto eliminada', showConfirmButton:false, timer:1200});
            table.ajax.reload(null, false);
          } else {
            Swal.fire({icon:'error', title:r.message || 'No se pudo eliminar'});
          }
        },
        error: function(){ Swal.fire({icon:'error', title:'Error al eliminar'}); }
      });
    });


    function showErrors(res){
      let error = '';
      if (res.errors){ $.each(res.errors, (k,v)=>{ error += (Array.isArray(v)? v.join('<br>'): v) + '<br>'; }); }
      Swal.fire({ icon:'error', title:'Error', html: error || 'Error al procesar' });
    }
    function errorToast(msg){ Swal.fire({ icon:'error', title:'Error', html: msg }); }

    // =======================
    //  MODAL ATRIBUTOS
    // =======================
    const $attrModal = $('#attrLinkModal');
    const $attrBody  = $attrModal.find('#pa_container');
    const $attrTitle = $attrModal.find('#pa_prod_name');
    const $attrSave  = $attrModal.find('#pa_save');
    let currentProductId = null;

    function renderGroup(group){
      const wrap = $(`
        <div class="card card-secondary mb-3">
          <div class="card-header py-2 sidebar-dark-primary">
            <div class="card-title m-0">
              <strong>${ group.name }</strong> <small class="text-white">(${ group.code })</small>
            </div>
          </div>
          <div class="card-body">
            <select class="form-control select2-attr" multiple style="width:100%"></select>
          </div>
        </div>
      `);
      const $select = wrap.find('select');

      // options: [{id, text}]
      const data = (group.options || []).map(o => ({ id: String(o.id), text: o.text }));

      $select.select2({
        data,
        dropdownParent: $attrModal,
        width: '100%',
        placeholder: 'Seleccione opciones…',
      });

      if (Array.isArray(group.selected) && group.selected.length){
        $select.val(group.selected.map(String)).trigger('change');
      }

      $select.data('group-id', group.id);
      return wrap;
    }

    // abrir modal de atributos
    $(document).on('click','.btn-attrs', function(e){
      e.preventDefault();
      const pid   = $(this).data('rowid');
      const pname = $(this).data('rowname') || 'Producto';

      currentProductId = pid;
      $attrTitle.text(pname);
      $attrBody.empty();

      $.getJSON(`/product/${pid}/attributes`, function(res){
        if (res.status !== 200){ return Swal.fire('Error','No se pudo cargar atributos.','error'); }

        // MUESTRA SOLO LOS HABILITADOS
        const groups = (res.groups || []).filter(g => !!g.enabled);

        if (!groups.length){
          $attrBody.html('<div class="alert alert-secondary mb-0">Este producto no tiene configuraciones de atributos habilitadas.</div>');
        } else {
          groups.forEach(g => $attrBody.append( renderGroup(g) ));
        }

        $attrModal.modal('show');
      }).fail(()=> Swal.fire('Error','Error al cargar atributos.','error'));
    });

    // guardar atributos
    $attrSave.on('click', function(){
      if (!currentProductId) return;
      const selectedAll = [];
      $attrBody.find('select.select2-attr').each(function(){
        ($(this).val() || []).forEach(v => selectedAll.push(v));
      });
      $.ajax({
        url: `/product/${currentProductId}/attributes`,
        type: 'POST',
        dataType: 'json',
        data: { _token: $('meta[name="csrf-token"]').attr('content'), options: selectedAll },
        success: function(r){
          if (r.status === 200){
            Swal.fire({icon:'success', title:'Guardado', timer:1200, showConfirmButton:false});
            $attrModal.modal('hide');
          } else {
            let err = '';
            if (r.errors) $.each(r.errors, (k,v)=> err += (Array.isArray(v)? v.join('<br>'): v) + '<br>');
            Swal.fire('Error', err || 'No se pudo guardar.', 'error');
          }
        },
        error: ()=> Swal.fire('Error','No se pudo guardar.','error')
      });
    });
  });
  </script>
@endsection
