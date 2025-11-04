@extends('store.layouts.app')

@section('title','Checkout - Datos del comprador (invitado)')

@section('content')
<div class="container py-5">
  <div class="row g-4">
    {{-- IZQ: Form invitado --}}
    <div class="col-lg-8">
      <div class="card glass-card border-0 p-3 text-light mb-4">
        <h5 class="mb-3"><i class="bi bi-person-plus me-2"></i>Datos del comprador</h5>
        <form id="form-guest">@csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-secondary">Nombre</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary">RUT</label>
              <input type="text" name="rut" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary">Correo</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary">Teléfono</label>
              <input type="text" name="phone" class="form-control" placeholder="+56 9 8888 8888">
            </div>
          </div>
          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" id="chkCreate">
            <label class="form-check-label small text-secondary" for="chkCreate">
              Crear una cuenta con estos datos al finalizar
            </label>
          </div>
        </form>
      </div>

      <div class="card glass-card border-0 p-3 text-light mb-4">
        <h5 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Dirección de entrega</h5>
        <form id="form-guest-address">@csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-secondary">Dirección principal</label>
              <input type="text" name="line1" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary">Complemento</label>
              <input type="text" name="line2" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label text-secondary">País</label>
              <select name="country_id" id="country_id" class="form-control select2"></select>
            </div>
            <div class="col-md-4">
              <label class="form-label text-secondary">Región</label>
              <select name="region_id" id="region_id" class="form-control select2"></select>
            </div>
            <div class="col-md-4">
              <label class="form-label text-secondary">Comuna</label>
              <select name="commune_id" id="commune_id" class="form-control select2"></select>
            </div>
            <div class="col-12">
              <label class="form-label text-secondary">Referencia</label>
              <input type="text" name="reference" class="form-control">
            </div>
          </div>
        </form>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ url('/store/checkout') }}" class="btn btn-outline-light">
          <i class="bi bi-arrow-left-short me-1"></i> Volver al detalle
        </a>
        <button class="btn btn-primary" id="btn-continue">Continuar a pago</button>
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
@endsection

@push('css')
  
  
@endpush

@push('scripts')
  <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<script>
  const csrf = "{{ csrf_token() }}";
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrf } });

  // Cascada País/Región/Comuna
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
        data: params => ({ search: params.term, country_id: $('#country_id').val() }),
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

document.addEventListener('DOMContentLoaded', () => {
  
  // Si el usuario está logueado accidentalmente, llévalo a step2
  (async () => {
    const r = await fetch('/store/profile/check'); const j = await r.json();
    if (j.logged) window.location.href = '/store/checkout/step2';
  })();

  async function fillSelect(url, sel, params={}){
    const qs = new URLSearchParams(params).toString();
    const r = await fetch(qs ? `${url}?${qs}` : url);
    const j = await r.json();
    sel.innerHTML = '<option value="">Seleccione…</option>';
    (j.data || []).forEach(it => {
      const opt = document.createElement('option');
      opt.value = it.id; opt.textContent = it.name;
      sel.appendChild(opt);
    });
  }

  // Mini resumen
  async function loadSummary(){
    const r = await fetch('/store/cart/summary'); const j = await r.json();
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

  // Continuar
  document.getElementById('btn-continue')?.addEventListener('click', () => {
    const f1 = Object.fromEntries(new FormData(document.getElementById('form-guest')));
    const f2 = Object.fromEntries(new FormData(document.getElementById('form-guest-address')));

    // Validación mínima
    if (!f1.name || !f1.email || !f2.line1 || !f2.country_id || !f2.region_id || !f2.commune_id) {
      Swal.fire('Faltan datos','Completa tu información y dirección para continuar.','warning');
      return;
    }

    // Aquí podrías hacer POST a /store/checkout/prepare (cuando lo implementes)
    // Por ahora, confirmamos y dejamos listos los datos (puedes guardarlos en localStorage si gustas):
    // localStorage.setItem('checkout_guest', JSON.stringify({ contact:f1, address:f2, create: document.getElementById('chkCreate').checked }));

    Swal.fire({icon:'success',title:'Datos listos',text:'Continuemos al pago…',timer:1200,showConfirmButton:false});
    // window.location.href = '/store/checkout/payment';
  });

  loadSummary();
});
</script>
@endpush
