@extends('store.layouts.app')

@section('title','Checkout - Pago')

@section('content')
<div class="container py-5">
  <div class="row g-4">
    {{-- IZQUIERDA: Pago --}}
    <div class="col-lg-8">
      <div class="card glass-card border-0 p-3 text-light mb-4">
        <h5 class="mb-3"><i class="bi bi-receipt me-2"></i> Documento tributario</h5>

        <div class="vstack gap-2 mb-3" id="doc-type-box">
          <label class="d-flex align-items-center gap-3 p-3 border rounded-3">
            <input class="form-check-input" type="radio" name="doc_type" value="boleta" checked>
            <div>
              <div class="fw-semibold">Boleta electrónica</div>
              <div class="small text-secondary">Para persona natural</div>
            </div>
          </label>
          <label class="d-flex align-items-center gap-3 p-3 border rounded-3">
            <input class="form-check-input" type="radio" name="doc_type" value="factura">
            <div>
              <div class="fw-semibold">Factura electrónica</div>
              <div class="small text-secondary">Requiere datos de empresa</div>
            </div>
          </label>
        </div>

        {{-- Campos FACTURA (se muestran sólo si se elige "factura") --}}
        <div id="factura-fields" class="border rounded-3 p-3 d-none">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small text-secondary mb-1">RUT empresa</label>
              <input type="text" class="form-control" id="f_rut" placeholder="12.345.678-9">
            </div>
            <div class="col-md-6">
              <label class="form-label small text-secondary mb-1">Razón social</label>
              <input type="text" class="form-control" id="f_name">
            </div>
            <div class="col-md-12">
              <label class="form-label small text-secondary mb-1">Giro</label>
              <input type="text" class="form-control" id="f_giro">
            </div>
            <div class="col-md-12">
              <label class="form-label small text-secondary mb-1">Dirección facturación</label>
              <input type="text" class="form-control" id="f_address">
            </div>
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
        </div>
      </div>

      <div class="card glass-card border-0 p-3 text-light mb-4">
        <h5 class="mb-3"><i class="bi bi-credit-card-2-front me-2"></i> Elige tu método de pago</h5>

        {{-- Métodos de pago (placeholder) --}}
        <div class="vstack gap-2" id="pay-methods">
          <label class="d-flex align-items-center gap-3 p-3 border rounded-3">
            <input class="form-check-input" type="radio" name="payment_method" value="webpay" checked>
            <div>
              <div class="fw-semibold">Tarjeta / Webpay</div>
              <div class="small text-secondary">Tarjeta de crédito o débito</div>
            </div>
          </label>
          <label class="d-flex align-items-center gap-3 p-3 border rounded-3">
            <input class="form-check-input" type="radio" name="payment_method" value="transfer">
            <div>
              <div class="fw-semibold">Transferencia bancaria</div>
              <div class="small text-secondary">Se confirmará manualmente</div>
            </div>
          </label>
        </div>

        {{-- Observaciones --}}
        <div class="mt-3">
          <label class="form-label text-secondary">Notas para tu pedido (opcional)</label>
          <textarea id="order_notes" class="form-control" rows="3" placeholder="Instrucciones, referencias, etc."></textarea>
        </div>

        {{-- Términos --}}
        <div class="form-check mt-3">
          <input class="form-check-input" type="checkbox" id="agree">
          <label class="form-check-label" for="agree">
            Acepto los <a href="#" class="link-light link-underline-opacity-50">términos y condiciones</a>.
          </label>
        </div>

        <div class="d-flex gap-2 mt-3">
          <a href="{{ url('/store/checkout/step2') }}" class="btn btn-outline-light">
            <i class="bi bi-arrow-left-short me-1"></i> Volver
          </a>
          <button id="btn-pay" class="btn btn-primary" disabled>
            <i class="bi bi-lock-fill me-1"></i> Pagar ahora
          </button>
        </div>
      </div>

      {{-- Contacto + Dirección seleccionada --}}
      <div class="card glass-card border-0 p-3 text-light">
        <h6 class="mb-3">Tus datos</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="small text-secondary">Nombre</div>
            <div id="u_name" class="fw-semibold">—</div>
          </div>
          <div class="col-md-6">
            <div class="small text-secondary">Correo</div>
            <div id="u_email" class="fw-semibold">—</div>
          </div>
          <div class="col-md-6">
            <div class="small text-secondary">RUT</div>
            <div id="u_rut" class="fw-semibold">—</div>
          </div>
          <div class="col-md-6">
            <div class="small text-secondary">Teléfono</div>
            <div id="u_phone" class="fw-semibold">—</div>
          </div>
          <div class="col-12">
            <div class="small text-secondary mt-2">Dirección de entrega</div>
            <div id="u_address" class="fw-semibold">—</div>
            <div class="small text-secondary" id="u_address_extra"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- DERECHA: Resumen --}}
    <div class="col-lg-4">
      <div class="card glass-card border-0 p-3 text-light sticky-top" style="top:80px">
        <h5 class="mb-3 border-bottom pb-2">Resumen</h5>
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
  $('#country_id').select2({
      placeholder: "Seleccione País...",
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

  // --------- Helpers ----------
  const fmt = n => (Math.round(n||0)).toLocaleString('es-CL');

  // --- Mostrar/ocultar campos de factura
  const docRadios = document.querySelectorAll('input[name="doc_type"]');
  const facturaBox = document.getElementById('factura-fields');
  docRadios.forEach(r => {
    r.addEventListener('change', () => {
      const val = document.querySelector('input[name="doc_type"]:checked')?.value;
      facturaBox.classList.toggle('d-none', val !== 'factura');
    });
  });
  // --------- Resumen del carrito ----------
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

  // --------- Datos de usuario + dirección principal ----------
  async function hydrateUserAndAddress(){
    // usuario
    const u = await fetch('/store/profile/check').then(r=>r.json()).catch(()=>({}));
    if (!u || !u.logged) { window.location.href = '/store/checkout/guest'; return; }

    document.getElementById('u_name').textContent  = u.user?.name  || '—';
    document.getElementById('u_email').textContent = u.user?.email || '—';
    document.getElementById('u_rut').textContent   = u.user?.rut   || '—';
    document.getElementById('u_phone').textContent = u.primary_phone?.number || '—';

    // direcciones → toma principal o primera
    const a = await fetch('/store/profile/addresses').then(r=>r.json()).catch(()=>({}));
    let addr = null;
    if (a?.status === 200 && Array.isArray(a.data)) {
      addr = a.data.find(x => x.is_primary) || a.data[0] || null;
    }
    // ...dentro de hydrateUserAndAddress()
    if (addr) {
      window.__selectedAddressId = addr.id; // <-- para enviarlo en place()
      const parts = [addr.line1||'', addr.line2||'', addr.commune?.name||'', addr.region?.name||''].filter(Boolean);
      document.getElementById('u_address').textContent = parts.join(', ');
      document.getElementById('u_address_extra').textContent = addr.reference || '';
    } else {
      document.getElementById('u_address').textContent = '—';
      document.getElementById('u_address_extra').textContent = 'Sin dirección. Debes agregar una en el paso anterior.';
    }
  }

  // --------- Estado del botón pagar ----------
  const agree = document.getElementById('agree');
  const btnPay = document.getElementById('btn-pay');
  agree.addEventListener('change', () => {
    btnPay.disabled = !agree.checked;
  });

  // --------- Acción Pagar (placeholder) ----------
  btnPay.addEventListener('click', async () => {
    if (!agree.checked) return;

    const docType = document.querySelector('input[name="doc_type"]:checked')?.value || 'boleta';

    // Si es FACTURA, validación rápida
    let facturaPayload = {};
    if (docType === 'factura') {
      const f_rut  = document.getElementById('f_rut').value.trim();
      const f_name = document.getElementById('f_name').value.trim();
      const f_giro = document.getElementById('f_giro').value.trim();
      const f_addr = document.getElementById('f_address').value.trim();
      const f_country = $('#country_id').val();  // <-- select2 correcto
      const f_region  = $('#region_id').val();
      const f_commune = $('#commune_id').val();

      if (!f_rut || !f_name || !f_giro || !f_addr || !f_country || !f_region || !f_commune) {
        return Swal.fire({icon:'warning', title:'Completa los datos de FACTURA.'});
      }

      facturaPayload = {
        receiver_rut: f_rut,
        receiver_name: f_name,
        receiver_giro: f_giro,
        receiver_address: f_addr,
        receiver_country_id: Number(f_country),
        receiver_region_id: Number(f_region),
        receiver_commune_id: Number(f_commune),
      };
    }

    // Método de pago + notas
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'webpay';
    const notes = document.getElementById('order_notes').value || null;

    // address_id si la tenemos (tómala de hydrateUserAndAddress)
    const shippingAddressId = window.__selectedAddressId || null;

    const payload = {
      doc_type: docType,
      payment_method: paymentMethod,
      notes: notes,
      ...(shippingAddressId ? { shipping_address_id: shippingAddressId } : {}),
      ...(docType === 'factura' ? facturaPayload : {}),
    };

    const res = await fetch('/store/checkout/place', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf},
      body: JSON.stringify(payload)
    });

    const j = await res.json();
    if (j.status === 200 && j.redirect) {
      window.location.href = j.redirect;
    } else {
      Swal.fire({icon:'error', title: j.message || 'No fue posible crear la orden.'});
    }
  });

  // init
  loadSummary();
  hydrateUserAndAddress();
});
</script>
@endpush

