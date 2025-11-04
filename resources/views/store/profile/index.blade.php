@extends('store.layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="container my-5">
  <div class="card text-light border-0 shadow-lg rounded-4">
    <div class=" border-secondary pb-0">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <button class="nav-link active text-light" data-bs-toggle="tab" data-bs-target="#pane-data" type="button">
            <i class="bi bi-person-circle me-1"></i> Datos personales
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link text-light" data-bs-toggle="tab" data-bs-target="#pane-orders" type="button">
            <i class="bi bi-bag-check me-1"></i> Mis compras
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link text-light" data-bs-toggle="tab" data-bs-target="#pane-pass" type="button">
            <i class="bi bi-key me-1"></i> Cambio de contraseña
          </button>
        </li>
      </ul>
    </div>

    <div class="card-body tab-content">
      {{-- Pestaña 1: Datos personales --}}
      <div class="tab-pane fade show active" id="pane-data">
        <div class="row g-4 align-items-start">
          {{-- Avatar --}}
          <div class="col-md-3 text-center">
            <img id="avatar-img" src="{{ $user->avatarUrl() }}" class="rounded-circle border border-secondary mb-3" width="140" height="140">
            <form id="form-avatar" enctype="multipart/form-data">@csrf
              <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*">
              <label for="avatar" class="btn btn-outline-light btn-sm"><i class="bi bi-upload me-1"></i> Cambiar foto</label>
            </form>
          </div>

          {{-- Datos personales --}}
          <div class="col-md-9">
            <form id="form-user">@csrf
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label text-secondary">Nombre</label>
                  <input type="text" name="name" value="{{ $user->name }}" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label text-secondary">RUT</label>
                  <input type="text" name="rut" value="{{ $user->rut }}" class="form-control">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label text-secondary">Correo electrónico</label>
                <input type="email" name="email" value="{{ $user->email }}" class="form-control">
              </div>
              <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar cambios</button>
            </form>
          </div>
        </div>

        <hr class="border-secondary my-4">

        {{-- Direcciones --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5><i class="bi bi-geo-alt me-1"></i> Mis direcciones</h5>
          <button class="btn btn-sm btn-outline-light" id="btn-add-address"><i class="bi bi-plus-circle me-1"></i> Nueva dirección</button>
        </div>
        <div id="address-list" class="row g-3"></div>

        <hr class="border-secondary my-4">

        {{-- Teléfonos --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5><i class="bi bi-telephone me-1"></i> Mis teléfonos</h5>
          <button class="btn btn-sm btn-outline-light" id="btn-add-phone"><i class="bi bi-plus-circle me-1"></i> Nuevo teléfono</button>
        </div>
        <div id="phone-list" class="row g-3"></div>
      </div>

      {{-- Pestaña 2: Mis compras --}}
      <div class="tab-pane fade" id="pane-orders">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5 class="m-0"><i class="bi bi-receipt me-2"></i> Mis compras</h5>
          <div class="d-flex gap-2">
            <select id="filterStatus" class="form-select form-select-sm" style="min-width:180px">
              <option value="">Todas</option>
              <option value="pending_payment">Pendiente de pago</option>
              <option value="paid">Pagada</option>
              <option value="processing">En preparación</option>
              <option value="shipped">Despachada</option>
              <option value="completed">Completada</option>
              <option value="cancelled">Cancelada</option>
            </select>
          </div>
        </div>

        <div id="orders-list" class="row g-3"></div>

        <div class="d-flex justify-content-between align-items-center mt-3" id="orders-pager" style="display:none">
          <button class="btn btn-outline-light btn-sm" id="btnPrev">Anterior</button>
          <div class="small text-secondary"><span id="pgCur">1</span> / <span id="pgLast">1</span></div>
          <button class="btn btn-outline-light btn-sm" id="btnNext">Siguiente</button>
        </div>
      </div>


      {{-- Pestaña 3: Cambio contraseña --}}
      <div class="tab-pane fade" id="pane-pass">
        <form id="form-pass" class="col-md-6 mx-auto">@csrf
          <div class="mb-3">
            <label class="form-label text-secondary">Contraseña actual</label>
            <input type="password" name="current_password" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label text-secondary">Nueva contraseña</label>
            <input type="password" name="new_password" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label text-secondary">Confirmar nueva contraseña</label>
            <input type="password" name="new_password_confirmation" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary"><i class="bi bi-key me-1"></i> Actualizar contraseña</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: DIRECCIONES --}}
<div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title"><i class="bi bi-geo-alt me-1"></i> Dirección</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="form-address">@csrf
          <input type="hidden" name="id">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label text-secondary">Dirección principal</label>
              <input type="text" name="line1" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary">Complemento</label>
              <input type="text" name="line2" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label text-secondary">País</label>
              <select id="country_id" name="country_id" class="form-select form-select-sm select2" style="width:100%"></select>
            </div>
            <div class="col-md-4">
              <label class="form-label text-secondary">Región</label>
              <select id="region_id" name="region_id" class="form-select select2" style="width:100%"></select>
            </div>
            <div class="col-md-4">
              <label class="form-label text-secondary">Comuna</label>
              <select id="commune_id" name="commune_id" class="form-select select2" style="width:100%"></select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label text-secondary">Referencia</label>
            <input type="text" name="reference" class="form-control">
          </div>
          <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" name="is_primary" id="chkPrimary" value="1">
            <label class="form-check-label small text-secondary" for="chkPrimary">Marcar como principal</label>
          </div>
        </form>
      </div>
      <div class="modal-footer border-secondary">
        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-save-address"><i class="bi bi-save me-1"></i> Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: TELÉFONO --}}
<div class="modal fade" id="phoneModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title"><i class="bi bi-telephone me-1"></i> Teléfono</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="form-phone">@csrf
          <input type="hidden" name="id">
          <div class="mb-3">
            <label class="form-label text-secondary">Número</label>
            <input type="text" name="number" class="form-control" placeholder="+56 9 8888 8888" required>
          </div>
          <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" name="is_default" id="chkDefault" value="1">
            <label class="form-check-label small text-secondary" for="chkDefault">Marcar como principal</label>
          </div>
        </form>
      </div>
      <div class="modal-footer border-secondary">
        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-save-phone"><i class="bi bi-save me-1"></i> Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: Detalle de orden --}}
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title"><i class="bi bi-receipt-cutoff me-2"></i> Detalle de compra</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="order-head" class="mb-3"></div>
        <div id="order-addr" class="small text-secondary mb-3"></div>
        <div id="order-items"></div>
        <div class="border-top pt-2 mt-3">
          <div class="d-flex justify-content-between small text-secondary">
            <span>Subtotal</span><span id="od-subtotal">$0</span>
          </div>
          <div class="d-flex justify-content-between small text-secondary">
            <span>IVA</span><span id="od-tax">$0</span>
          </div>
          <div class="d-flex justify-content-between fw-bold">
            <span>Total</span><span id="od-total">$0</span>
          </div>
        </div>
      </div>
      <div class="modal-footer border-secondary">
        <a id="od-view" target="_blank" class="btn btn-outline-light">
          <i class="bi bi-box-arrow-up-right me-1"></i> Ver comprobante
        </a>
        <button class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


@endsection

@push('styles')
  {{-- Cropper.js --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <style>
    .select2-container--default .select2-selection--single{
        background-color: rgba(255,255,255,.08);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.3rem;
        color:#ddd;
    }

    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #ddd;
        color:#212529
    }

    .select2-container--default .select2-results__option--selected {
        background-color: #ddd;
        color:#212529
    }

    .select2-dropdown{
        background-color: #212529;
    }
  </style>
@endpush

@push('scripts')
<script>
    const $dp       = $('#addressModal'); // dropdownParent para select2

    $('#country_id').select2({
        placeholder: "Seleccione País...",
        dropdownParent: $dp,
        width: '100%',
        ajax: {
        type: "GET",
        url: "/country",
        data: params => ({ search: params.term }),
        processResults: data => ({ results: data.data })
        },
        templateResult: d => d.loading ? d.text : $('<div>' + d.name + '</div>'),
        templateSelection: d => d.name || d.text,
    });

    // === Región (dependiente del país) ===
    $('#region_id').select2({
        placeholder: "Seleccione Región...",
        dropdownParent: $dp,
        width: '100%',
        ajax: {
        type: "GET",
        url: "/region",
        data: params => {
            return {
            search: params.term,
            country_id: $('#country_id').val() // país actual
            };
        },
        processResults: data => ({ results: data.data })
        },
        templateResult: d => d.loading ? d.text : $('<div>' + d.name + '</div>'),
        templateSelection: d => d.name || d.text,
    });

    // === Comuna (dependiente de la región) ===
    $('#commune_id').select2({
        placeholder: "Seleccione Comuna...",
        dropdownParent: $dp,
        width: '100%',
        ajax: {
        type: "GET",
        url: "/commune",
        data: params => {
            return {
            search: params.term,
            region_id: $('#region_id').val() // región actual
            };
        },
        processResults: data => ({ results: data.data })
        },
        templateResult: d => d.loading ? d.text : $('<div>' + d.name + '</div>'),
        templateSelection: d => d.name || d.text,
    });

    $(document).ready(function() {
        // --- Datos personales ---
        $('#form-user').on('submit', async e => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(e.target));
            const res = await fetch('/store/profile/update', {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.CSRF_TOKEN}, body:JSON.stringify(data)});
            const j = await res.json();
            Swal.fire({toast:true,icon:j.status===200?'success':'error',title:j.message||'Error',timer:1800,showConfirmButton:false});
        });

        // --- Avatar ---
        $('#avatar').on('change', async function(){
            const fd=new FormData($('#form-avatar')[0]);
            const res=await fetch('/store/profile/avatar',{method:'POST',headers:{'X-CSRF-TOKEN':window.CSRF_TOKEN},body:fd});
            const j=await res.json();
            if(j.status===200){$('#avatar-img').attr('src',j.avatar_url);Swal.fire({toast:true,icon:'success',title:'Foto actualizada',timer:1500,showConfirmButton:false});}
        });

        // --- Contraseña ---
        $('#form-pass').on('submit', async e=>{
            e.preventDefault();
            const data=Object.fromEntries(new FormData(e.target));
            const res=await fetch('/store/profile/password',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.CSRF_TOKEN},body:JSON.stringify(data)});
            const j=await res.json();
            Swal.fire({toast:true,icon:j.status===200?'success':'error',title:j.message||'Error',timer:1800,showConfirmButton:false});
            if(j.status===200)e.target.reset();
        });

        // --- Direcciones ---
        const addrModal=new bootstrap.Modal('#addressModal');
        const addrList=$('#address-list');

        async function loadAddresses(){
            const r=await fetch('/store/profile/addresses');const j=await r.json();
            if(j.status===200){
            addrList.html(j.data.length?j.data.map(a=>cardAddress(a)).join(''):'<div class="text-secondary">No tienes direcciones registradas.</div>');
            bindAddressButtons();
            }
        }
        function cardAddress(a){
            return `<div class="col-md-6">
            <div class="card bg-secondary bg-opacity-25 border-secondary p-3 h-100">
                <div class="d-flex justify-content-between">
                <strong>${a.line1||''}</strong>
                <div>
                    <button class="btn btn-sm btn-outline-light js-edit" data-id="${a.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger js-del" data-id="${a.id}"><i class="bi bi-trash"></i></button>
                </div>
                </div>
                <div class="small text-secondary">${a.line2||''}</div>
                <div class="small">${a.commune?.name||''}, ${a.region?.name||''}</div>
                ${a.is_primary?'<span class="badge bg-primary mt-2">Principal</span>':''}
            </div>
            </div>`;
        }

        function bindAddressButtons(){
            $('.js-del').on('click',async e=>{
            const id=e.currentTarget.dataset.id;
            const ok=await Swal.fire({title:'¿Eliminar dirección?',icon:'warning',showCancelButton:true});
            if(!ok.isConfirmed)return;
            await fetch(`/store/profile/addresses/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':window.CSRF_TOKEN}});
            loadAddresses();
            });
            $('.js-edit').on('click', async e=>{
            const id=e.currentTarget.dataset.id;
            const res=await fetch(`/store/profile/addresses/${id}`);const j=await res.json();
            fillAddressModal(j.data); addrModal.show();
            });
        }

        $('#btn-add-address').on('click',()=>{fillAddressModal(null);addrModal.show();});

        // --- Direcciones ---
        async function fillAddressModal(data) {
            const $form = $('#form-address');
            $form[0].reset();
            $form.find('[name=id]').val(data ? data.id : '');
            $('#chkPrimary').prop('checked', data?.is_primary ?? false);

            $('#country_id').find('option').remove();  // elimina opciones anteriores
            $('#region_id').find('option').remove();
            $('#commune_id').find('option').remove();

            $('#country_id').val(null).trigger('change');
            $('#region_id').val(null).trigger('change');
            $('#commune_id').val(null).trigger('change');

            // === Modo edición ===
            if (data) {
                // País
                if (data.country) {
                    const opt = new Option(data.country.name, data.country.id, true, true);
                    $('#country_id').append(opt).trigger('change');
                }

                // Región
                if (data.region) {
                    const opt = new Option(data.region.name, data.region.id, true, true);
                    $('#region_id').append(opt).trigger('change');
                }

                // Comuna
                if (data.commune) {
                    const opt = new Option(data.commune.name, data.commune.id, true, true);
                    $('#commune_id').append(opt).trigger('change');
                }

                // Campos texto
                $form.find('[name=line1]').val(data.line1 || '');
                $form.find('[name=line2]').val(data.line2 || '');
                $form.find('[name=reference]').val(data.reference || '');
            }
            }


        $('#btn-save-address').on('click', async()=>{
            const data=Object.fromEntries(new FormData($('#form-address')[0]));
            data.is_primary=$('#chkPrimary').is(':checked')?1:0;
            const r=await fetch('/store/profile/addresses',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.CSRF_TOKEN},body:JSON.stringify(data)});
            const j=await r.json();
            if(j.status===200){addrModal.hide();Swal.fire({toast:true,icon:'success',title:'Guardado',timer:1200,showConfirmButton:false});loadAddresses();}
        });

        // --- Teléfonos ---
        const phoneModal=new bootstrap.Modal('#phoneModal');
        const phoneList=$('#phone-list');

        async function loadPhones(){
            const r=await fetch('/store/profile/phones');const j=await r.json();
            if(j.status===200){
            phoneList.html(j.data.length?j.data.map(p=>cardPhone(p)).join(''):'<div class="text-secondary">No tienes teléfonos registrados.</div>');
            bindPhoneButtons();
            }
        }

        function cardPhone(p){
            return `<div class="col-md-4">
            <div class="card bg-secondary bg-opacity-25 border-secondary p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                <strong>${p.number}</strong>
                <div>
                    <button class="btn btn-sm btn-outline-light js-edit-phone" data-id="${p.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger js-del-phone" data-id="${p.id}"><i class="bi bi-trash"></i></button>
                </div>
                </div>
                ${p.is_default?'<span class="badge bg-primary mt-2">Principal</span>':''}
            </div>
            </div>`;
        }

        function bindPhoneButtons(){
            $('.js-del-phone').on('click',async e=>{
            const id=e.currentTarget.dataset.id;
            const ok=await Swal.fire({title:'¿Eliminar teléfono?',icon:'warning',showCancelButton:true});
            if(!ok.isConfirmed)return;
            await fetch(`/store/profile/phones/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':window.CSRF_TOKEN}});
            loadPhones();
            });
            $('.js-edit-phone').on('click',async e=>{
            const id=e.currentTarget.dataset.id;
            const res=await fetch(`/store/profile/phones/${id}`);const j=await res.json();
            fillPhoneModal(j.data); phoneModal.show();
            });
        }

        $('#btn-add-phone').on('click',()=>{fillPhoneModal(null);phoneModal.show();});
        $('#btn-save-phone').on('click',async()=>{
            const data=Object.fromEntries(new FormData($('#form-phone')[0]));
            data.is_default=$('#chkDefault').is(':checked')?1:0;
            const r=await fetch('/store/profile/phones',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.CSRF_TOKEN},body:JSON.stringify(data)});
            const j=await r.json();
            if(j.status===200){phoneModal.hide();Swal.fire({toast:true,icon:'success',title:'Guardado',timer:1200,showConfirmButton:false});loadPhones();}
        });

        function fillPhoneModal(p){
            $('#form-phone')[0].reset();
            $('#form-phone [name=id]').val(p?p.id:'');
            $('#form-phone [name=number]').val(p?p.number:'');
            $('#chkDefault').prop('checked',p?.is_default??false);
        }
        // --- Carga inicial ---
        loadAddresses();
        loadPhones();
    });
</script>

<script>
(function(){
  const fmt = n => (n||0).toLocaleString('es-CL');

  // Estado de paginación
  let curPage = 1, lastPage = 1, curStatus = '';

  // Cargar listado
  async function loadOrders(page = 1){
    const url = new URL(`{{ route('store.profile.orders.index') }}`, window.location.origin);
    url.searchParams.set('page', page);
    if(curStatus) url.searchParams.set('status', curStatus);

    const res = await fetch(url);
    const j = await res.json();

    if(j.status !== 200){ 
      document.getElementById('orders-list').innerHTML = '<div class="text-secondary">No fue posible cargar tus compras.</div>';
      document.getElementById('orders-pager').style.display = 'none';
      return;
    }

    renderOrders(j.data || []);
    // pager
    curPage  = j.meta.current_page;
    lastPage = j.meta.last_page;
    document.getElementById('pgCur').innerText  = curPage;
    document.getElementById('pgLast').innerText = lastPage;
    document.getElementById('orders-pager').style.display = (lastPage > 1 ? '' : 'none');
  }

  // Render cards
  function renderOrders(rows){
    const box = document.getElementById('orders-list');
    if(!rows.length){
      box.innerHTML = '<div class="text-secondary">Aún no tienes compras.</div>';
      return;
    }
    box.innerHTML = rows.map(o => cardOrder(o)).join('');
    bindOrderButtons();
  }

  function badgeStatus(s){
    const map = {
      pending_payment: 'warning',
      paid: 'success',
      processing: 'info',
      shipped: 'primary',
      completed: 'success',
      cancelled: 'danger',
    };
    const cls = map[s] || 'secondary';
    const label = ({
      pending_payment:'Pendiente de pago',
      paid:'Pagada',
      processing:'En preparación',
      shipped:'Despachada',
      completed:'Completada',
      cancelled:'Cancelada'
    })[s] || s;
    return `<span class="badge bg-${cls}">${label}</span>`;
  }

  function cardOrder(o){
    return `
      <div class="col-12">
        <div class="card bg-secondary bg-opacity-25 border-secondary p-3 hover-shadow">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
              <div class="small text-secondary">Orden #${o.number}</div>
              <div class="fw-semibold">Creada: ${o.created_at}</div>
              <div class="small">Ítems: ${o.items_count}</div>
            </div>
            <div class="text-end">
              <div class="mb-1">${badgeStatus(o.status)}</div>
              <div class="fw-bold">${o.currency} $${fmt(o.grand_total)}</div>
            </div>
          </div>
          <div class="mt-2 d-flex flex-wrap gap-2">
            <button class="btn btn-outline-light btn-sm js-view" data-id="${o.id}">
              <i class="bi bi-eye me-1"></i> Ver detalle
            </button>
            <a class="btn btn-outline-light btn-sm" target="_blank" href="/store/profile/orders/${o.public_uid}/document">
              <i class="bi bi-box-arrow-up-right me-1"></i> Ver comprobante
            </a>
            <button class="btn btn-primary btn-sm js-repeat" data-id="${o.id}">
              <i class="bi bi-arrow-repeat me-1"></i> Repetir compra
            </button>
          </div>
        </div>
      </div>
    `;
  }


  function bindOrderButtons(){
    document.querySelectorAll('.js-view').forEach(btn=>{
      btn.addEventListener('click', async (e)=>{
        const id = e.currentTarget.dataset.id;
        const res = await fetch(`{{ url('/store/profile/orders') }}/${id}`);
        const j = await res.json();
        if(j.status !== 200) return;

        const od = j.order;
        // header
        document.getElementById('order-head').innerHTML = `
          <div class="d-flex justify-content-between">
            <div>
              <div class="small text-secondary">Orden #${od.number}</div>
              <div class="fw-semibold">Creada: ${od.created_at}</div>
              <div class="small">Estado: ${badgeStatus(od.status)}</div>
            </div>
            <div class="text-end">
              <div class="small text-secondary">Total</div>
              <div class="fs-5 fw-bold">${od.currency} $${fmt(od.total)}</div>
            </div>
          </div>
        `;
        // address
        const a = od.shipping;
        document.getElementById('order-addr').innerHTML = a ? `
          <div><i class="bi bi-geo-alt me-1"></i>
            ${a.line1 || ''} ${a.line2 ? ' · '+a.line2 : ''}</div>
          <div>${[a.commune_name,a.region_name,a.country_name].filter(Boolean).join(', ')}</div>
          ${a.reference ? `<div class="text-secondary">Ref.: ${a.reference}</div>` : ''}
        ` : '<div class="text-secondary">Sin dirección registrada.</div>';

        // items
        const list = (od.items || []).map(it => `
          <div class="card border-0 bg-dark-subtle p-2 mb-2">
            <div class="d-flex align-items-center gap-2">
              <img src="${it.thumb || '/img/no-image.jpg'}" class="rounded" width="56" height="56" style="object-fit:cover">
              <div class="flex-grow-1">
                <div class="fw-semibold">${it.product_name}</div>
                <div class="small text-secondary">
                  ${(it.options_display||[]).map(o=>`${o.group}: ${o.value}`).join(' · ')}
                </div>
                <div class="small">Cantidad: ${it.qty}</div>
              </div>
              <div class="text-end">
                <div class="small text-secondary">Total ítem</div>
                <div class="fw-bold">$${fmt(it.line_total)}</div>
              </div>
            </div>
          </div>
        `).join('');
        document.getElementById('order-items').innerHTML = list || '<div class="text-secondary">Sin ítems.</div>';

        // totals
        document.getElementById('od-subtotal').innerText = '$' + fmt(od.subtotal);
        document.getElementById('od-tax').innerText      = '$' + fmt(od.tax);
        document.getElementById('od-total').innerText    = '$' + fmt(od.total);

        // comprobante
        document.getElementById('od-view').href = `/store/profile/orders/${od.public_uid}/document`;

        new bootstrap.Modal('#orderModal').show();
      });
    });
    document.querySelectorAll('.js-repeat').forEach(btn=>{
      btn.addEventListener('click', async e=>{
        const id = e.currentTarget.dataset.id;

        const ok = await Swal.fire({
          title: '¿Repetir esta compra?',
          text: 'Se volverán a agregar los productos al carrito con los valores actuales.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Sí, repetir',
          cancelButtonText: 'Cancelar'
        });
        if(!ok.isConfirmed) return;

        Swal.fire({
          title: 'Agregando productos...',
          allowOutsideClick: false,
          didOpen: ()=> Swal.showLoading()
        });

        const res = await fetch(`/store/orders/${id}/repeat`, {
          method: 'POST',
          headers: {'X-CSRF-TOKEN': window.CSRF_TOKEN}
        });
        const j = await res.json();
        Swal.close();

        if(j.status === 200){
          Swal.fire({
            icon: 'success',
            title: 'Productos agregados al carrito',
            showConfirmButton: false,
            timer: 1500
          });
          setTimeout(()=> window.location.href = `${j.redirect}`, 1200);
        } else {
          Swal.fire({icon:'error', title: j.message || 'No se pudo repetir la compra'});
        }
      });
    });

  }

  // Filtro por estado
  document.getElementById('filterStatus').addEventListener('change', (e)=>{
    curStatus = e.target.value || '';
    loadOrders(1);
  });

  // Paginación
  document.getElementById('btnPrev').addEventListener('click', ()=>{
    if(curPage>1) loadOrders(curPage-1);
  });
  document.getElementById('btnNext').addEventListener('click', ()=>{
    if(curPage<lastPage) loadOrders(curPage+1);
  });

  // Cargar cuando el usuario entra a la pestaña “Mis compras”
  document.querySelector('button[data-bs-target="#pane-orders"]')
    ?.addEventListener('shown.bs.tab', ()=> loadOrders(1));

  // Opcional: si quieres precargar en primera visita al perfil:
  // loadOrders(1);
})();
</script>

@endpush
