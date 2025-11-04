@extends('store.layouts.app')

@section('title','Checkout - Datos de contacto y envío')

@section('content')
<div class="container py-5">
  <div class="row g-4">
    {{-- IZQ: Datos --}}
    <div class="col-lg-8">
      {{-- Contacto --}}
      <div class="card glass-card border-0 p-3 text-light mb-4">
        <h5 class="mb-3"><i class="bi bi-person-vcard me-2"></i>Datos de contacto</h5>
        <form id="form-contact">@csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-secondary">Nombre</label>
              <input type="text" name="name" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary">RUT</label>
              <input type="text" name="rut" class="form-control">
            </div>
            <div class="col-md-8">
              <label class="form-label text-secondary">Correo</label>
              <input type="email" name="email" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label text-secondary">Teléfono</label>
              <input type="text" name="phone" class="form-control" placeholder="+56 9 8888 8888">
            </div>
          </div>
        </form>
      </div>

      {{-- Dirección --}}
      <div class="card glass-card border-0 p-3 text-light mb-4">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Dirección de entrega</h5>
          <button class="btn btn-sm btn-outline-light" id="btn-add-address">
            <i class="bi bi-plus-circle me-1"></i> Nueva dirección
          </button>
        </div>

        <div id="address-list" class="mt-3">
          <div class="text-secondary small">Cargando direcciones…</div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ url('/store/checkout') }}" class="btn btn-outline-light">
          <i class="bi bi-arrow-left-short me-1"></i> Volver al detalle
        </a>
        <button class="btn btn-primary" id="btn-continue">
          Continuar a pago
        </button>
      </div>
    </div>

    {{-- DER: Resumen --}}
    <div class="col-lg-4">
      <div class="card glass-card border-0 p-3 text-light sticky-top" style="top:80px">
        <h5 class="mb-3 border-bottom pb-2">Resumen de compra</h5>
        <div id="mini-items" class="mb-3"></div>
        <div class="d-flex justify-content-between small text-secondary mb-2">
          <span>Subtotal</span> <span id="subtotal">$0</span>
        </div>
        <div class="d-flex justify-content-between small text-secondary mb-2">
          <span>IVA (19%)</span> <span id="iva">$0</span>
        </div>
        <div class="d-flex justify-content-between fw-bold border-top pt-2">
          <span>Total</span> <span id="total">$0</span>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- MODAL DIRECCIÓN (con Select2 anidado como en Perfil) --}}
<div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title"><i class="bi bi-geo-alt me-1"></i> Dirección</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
              <select id="country_id" name="country_id" class="form-select select2" style="width:100%"></select>
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
            <input class="form-check-input" type="checkbox" id="chkPrimary" name="is_primary" value="1">
            <label class="form-check-label small text-secondary" for="chkPrimary">Marcar como principal</label>
          </div>
        </form>
      </div>
      <div class="modal-footer border-secondary">
        <button class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" id="btn-save-address"><i class="bi bi-save me-1"></i> Guardar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
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
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
  <script>
    const $dp  = $('#addressModal'); // dropdownParent como en Perfil
    // =============== SELECT2 anidados (igual que Perfil) ===============
    // Init global (una sola vez)
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
      templateResult: d => d.loading ? d.text : $('<div>'+ d.name +'</div>'),
      templateSelection: d => d.name || d.text,
    });

    $('#region_id').select2({
      placeholder: "Seleccione Región...",
      dropdownParent: $dp,
      width: '100%',
      ajax: {
        type: "GET",
        url: "/region",
        data: params => ({
          search: params.term,
          country_id: $('#country_id').val()
        }),
        processResults: data => ({ results: data.data })
      },
      templateResult: d => d.loading ? d.text : $('<div>'+ d.name +'</div>'),
      templateSelection: d => d.name || d.text,
    });

    $('#commune_id').select2({
      placeholder: "Seleccione Comuna...",
      dropdownParent: $dp,
      width: '100%',
      ajax: {
        type: "GET",
        url: "/commune",
        data: params => ({
          search: params.term,
          region_id: $('#region_id').val()
        }),
        processResults: data => ({ results: data.data })
      },
      templateResult: d => d.loading ? d.text : $('<div>'+ d.name +'</div>'),
      templateSelection: d => d.name || d.text,
    });

  document.addEventListener('DOMContentLoaded', () => {
    const csrf = "{{ csrf_token() }}";
    
    const addrBox   = document.getElementById('address-list');
    const addrModal = new bootstrap.Modal('#addressModal');

    // =============== CONTACTO (autorrelleno) ===============
    (async function hydrateUser(){
      const r = await fetch('/store/profile/check');
      const j = await r.json();
      if (!j.logged) { window.location.href = '/store/checkout/guest'; return; }
      const f = document.getElementById('form-contact');
      f.name.value  = j.user?.name  || '';
      f.email.value = j.user?.email || '';
      f.rut.value   = j.user?.rut   || '';
      f.phone.value = j.primary_phone?.number || '';
    })();

    // =============== LISTADO DE DIRECCIONES ===============
    function cardAddress(a){
      const label = [
        a.line1,
        a.line2 ? `, ${a.line2}` : '',
        (a.commune?.name && a.region?.name) ? `<div class="small text-secondary">${a.commune.name}, ${a.region.name}</div>` : ''
      ].join('');
      return `
        <label class="d-flex align-items-start gap-3 p-3 border rounded-3 mb-2 hover-shadow"
               style="cursor:pointer;background:rgba(255,255,255,.04);">
          <input type="radio" name="address_id" class="form-check-input mt-1" value="${a.id}" ${a.is_primary ? 'checked' : ''}>
          <div class="flex-grow-1">
            ${label}
            ${(a.is_primary?'<span class="badge bg-primary mt-2">Principal</span>':'')}
          </div>
          <div class="ms-auto d-flex gap-2">
            <button class="btn btn-sm btn-outline-light js-edit" data-id="${a.id}"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-sm btn-outline-danger js-del" data-id="${a.id}"><i class="bi bi-trash"></i></button>
          </div>
        </label>`;
    }

    async function loadAddresses(){
      addrBox.innerHTML = '<div class="text-secondary small">Cargando…</div>';
      const r = await fetch('/store/profile/addresses');
      const j = await r.json();
      if (j.status === 200) {
        addrBox.innerHTML = (j.data && j.data.length)
          ? j.data.map(cardAddress).join('')
          : '<div class="text-secondary">No tienes direcciones. Crea una para continuar.</div>';
        bindAddressButtons();
      } else {
        addrBox.innerHTML = '<div class="text-danger small">No se pudieron cargar direcciones.</div>';
      }
    }

    function bindAddressButtons(){
      // NUEVA dirección (igual que Perfil)
      document.getElementById('btn-add-address')?.addEventListener('click', () => {
        fillAddressModal(null); addrModal.show();
      });

      // Editar
      addrBox.querySelectorAll('.js-edit').forEach(btn => {
        btn.addEventListener('click', async () => {
          const id = btn.dataset.id;
          const r = await fetch(`/store/profile/addresses/${id}`);
          const j = await r.json();
          fillAddressModal(j.data || null); addrModal.show();
        });
      });

      // Eliminar
      addrBox.querySelectorAll('.js-del').forEach(btn => {
        btn.addEventListener('click', async () => {
          const id = btn.dataset.id;
          const ok = await Swal.fire({title:'¿Eliminar dirección?',icon:'warning',showCancelButton:true});
          if (!ok.isConfirmed) return;
          await fetch(`/store/profile/addresses/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN': csrf}});
          loadAddresses();
        });
      });
    }

    

    // =============== FILL MODAL (mismo comportamiento que Perfil) ===============
    async function fillAddressModal(data){
      const $f = $('#form-address');
      $f[0].reset();
      $f.find('[name=id]').val(data ? data.id : '');
      $('#chkPrimary').prop('checked', data?.is_primary ?? false);

      // Limpieza “trigger(null)” para que NO queden valores tomados
      $('#country_id').val(null).trigger('change');
      $('#region_id').val(null).trigger('change');
      $('#commune_id').val(null).trigger('change');

      // Edición → precargamos opciones actuales en Select2
      if (data) {
        if (data.country) {
          const opt = new Option(data.country.name, data.country.id, true, true);
          $('#country_id').append(opt).trigger('change');
        }
        if (data.region) {
          const opt = new Option(data.region.name, data.region.id, true, true);
          $('#region_id').append(opt).trigger('change');
        }
        if (data.commune) {
          const opt = new Option(data.commune.name, data.commune.id, true, true);
          $('#commune_id').append(opt).trigger('change');
        }
        // Text inputs
        $f.find('[name=line1]').val(data.line1 || '');
        $f.find('[name=line2]').val(data.line2 || '');
        $f.find('[name=reference]').val(data.reference || '');
      }
    }

    // Guardar dirección (igual que Perfil)
    document.getElementById('btn-save-address')?.addEventListener('click', async () => {
      const data = Object.fromEntries(new FormData(document.getElementById('form-address')));
      data.is_primary = document.getElementById('chkPrimary').checked ? 1 : 0;

      const r = await fetch('/store/profile/addresses', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN': csrf},
        body: JSON.stringify(data)
      });
      const j = await r.json();
      if (j.status === 200) {
        addrModal.hide();
        Swal.fire({toast:true,icon:'success',title:'Dirección guardada',timer:1200,showConfirmButton:false});
        await loadAddresses();
        // Si marcó principal, queda seleccionada
        if (data.is_primary) {
          const radio = document.querySelector(`input[name="address_id"][value="${j.data?.id ?? ''}"]`);
          radio && (radio.checked = true);
        }
      } else {
        Swal.fire('Error', j.message || 'No se pudo guardar', 'error');
      }
    });

    // =============== MINI RESUMEN ===============
    async function loadSummary(){
      const r = await fetch('/store/cart/summary');
      const j = await r.json();
      if (j.status !== 200) return;
      const it = j.summary.items || [];
      document.getElementById('mini-items').innerHTML = it.map(row => `
        <div class="d-flex align-items-start gap-2 mb-2">
          <img src="${row.thumb}" class="rounded" width="48" height="48" style="object-fit:cover">
          <div class="flex-grow-1">
            <div class="small">${row.product.name}</div>
            <div class="text-secondary small">${row.options_display?.map(o=>`${o.group}: ${o.value}`).join(' · ') || ''}</div>
          </div>
          <div class="small fw-semibold">$${fmt(row.line_net)}</div>
        </div>
      `).join('');
      const t = j.summary.totals || {};
      document.getElementById('subtotal').textContent = '$' + fmt(t.subtotal);
      document.getElementById('iva').textContent      = '$' + fmt(t.tax);
      document.getElementById('total').textContent    = '$' + fmt(t.total);
    }
    const fmt = n => (Math.round(n||0)).toLocaleString('es-CL');

    // Continuar a pago: exige dirección seleccionada
    document.getElementById('btn-continue')?.addEventListener('click', async ()=>{
      const selected = document.querySelector('input[name="address_id"]:checked');
      if (!selected) {
        Swal.fire('Falta dirección', 'Selecciona o crea una dirección para continuar.', 'warning');
        return;
      }
      // Aquí podrás enviar al backend la address_id antes de pasar al pago.
      //Swal.fire({icon:'success',title:'Datos listos',text:'Dirección y contacto cargados.',timer:1200,showConfirmButton:false});
      window.location.href = '/store/checkout/payment';
    });

    // init
    loadAddresses();
    loadSummary();
  });
  </script>
@endpush
