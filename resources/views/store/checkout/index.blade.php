@extends('store.layouts.app')

@section('title', 'Checkout - Detalle de compra')

@section('content')
<div class="container py-5">

  <div class="row g-4">
    {{-- === Columna izquierda: Detalle === --}}
    <div class="col-lg-8">
      <div class="card glass-card border-0 p-3 mb-4 text-light">
        <h5 class="mb-3"><i class="bi bi-cart-check me-1"></i> Detalle de tu compra</h5>
        <div id="cart-items"></div>
      </div>
    </div>

    {{-- === Columna derecha: Resumen === --}}
    <div class="col-lg-4">
      <div class="card glass-card border-0 p-3 text-light sticky-top" style="top:80px">
        <h5 class="mb-3 border-bottom pb-2">Resumen de compra</h5>
        <div class="d-flex justify-content-between small text-secondary mb-2">
          <span>Subtotal</span> <span id="subtotal">$0</span>
        </div>
        <div class="d-flex justify-content-between small text-secondary mb-2">
          <span>IVA (19%)</span> <span id="iva">$0</span>
        </div>
        <div class="d-flex justify-content-between fw-bold border-top pt-2 mb-3">
          <span>Total</span> <span id="total">$0</span>
        </div>
        <div class="d-grid gap-2">
          <a href="{{ route('index') }}" class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-1"></i> Volver
          </a>
          <button id="btn-continue" class="btn btn-primary py-2">
            Continuar compra
          </button>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
  const csrf = "{{ csrf_token() }}";
  const cartBox = document.getElementById('cart-items');

  // === Cargar carrito ===
  async function loadCart(){
    const res = await fetch('/store/cart/summary');
    const j = await res.json();
    if (j.status !== 200 || !j.summary) {
      cartBox.innerHTML = '<p class="text-secondary">Tu carrito está vacío.</p>';
      return;
    }
    const summary = j.summary;
    renderCart(summary.items || []);
    updateTotals(summary);
  }

  // === Renderizar productos ===
  function renderCart(items){
    if(!items.length){
      cartBox.innerHTML = '<p class="text-secondary">Tu carrito está vacío.</p>';
      return;
    }

    cartBox.innerHTML = items.map(item => {
      const isFixedQty = !!item.product?.uses_quantity;

      // ID “real” actual de cantidad para selects (en este orden de preferencia)
      const currentQtyId = (item.options_map && item.options_map.quantity_id)
        ? item.options_map.quantity_id
        : (item.qty_raw ?? item.qty);

      const qtyInput = isFixedQty
        ? `<select class="form-select form-select-sm js-qty" data-id="${item.row_id}" style="width:100%">
            <option value="${currentQtyId}" selected>${item.qty}</option>
          </select>`
        : `<input type="number" min="1" value="${item.qty}" 
            data-id="${item.row_id}" class="form-control form-control-sm text-center js-qty">`;


      return `
        <div class="card mb-3 p-3 border-0 bg-dark-subtle">
          <div class="row align-items-center g-2">
            <div class="col-md-2 text-center">
              <img src="${item.thumb || '/img/no-image.jpg'}" class="img-fluid rounded" style="max-height:80px;object-fit:cover;">
            </div>
            <div class="col-md-6">
              <h6 class="mb-1 text-white">${item.product?.name || 'Producto'}</h6>
              <div class="small text-secondary">
                ${Array.isArray(item.options_display)
                  ? item.options_display.map(o => `<div>${o.group}: ${o.value}</div>`).join('')
                  : (item.options || '')}
              </div>
              <button class="btn btn-link text-danger p-0 mt-1 js-del" data-id="${item.row_id}">
                <i class="bi bi-trash"></i> Eliminar
              </button>
            </div>
            <div class="col-md-2">${qtyInput}</div>
            <div class="col-md-2 text-end">
              <div class="fw-bold">$${formatNumber(item.line_net)}</div>
            </div>
          </div>
        </div>`;
    }).join('');

    // Inicializa select2 para los que usan cantidades predefinidas
    items.forEach(item => {
    if (item.product?.uses_quantity) {
      const currentQtyId = (item.options_map && item.options_map.quantity_id)
        ? item.options_map.quantity_id
        : (item.qty_raw ?? item.qty);
      initQtySelect(item.row_id, item.product.id, currentQtyId, item.options_map);
    }
  });


    bindCartButtons();
  }

  // === Inicializar select2 de cantidad ===
    async function initQtySelect(rowId, productId, currentQty, currentOptions = {}) {
        const sel = document.querySelector(`select[data-id="${rowId}"]`);
        
        // Envía los atributos actuales como parámetros
        const params = new URLSearchParams(currentOptions).toString();
        const res = await fetch(`/store/product/${productId}/quantities?${params}`);
        const j = await res.json();

        sel.innerHTML = '';
        if (j.status === 200 && j.data.length) {
            j.data.forEach(optData => {
            const opt = document.createElement('option');
            opt.value = optData.id;
            opt.textContent = optData.name;
            if (String(optData.id) === String(currentQty)) opt.selected = true;
            sel.appendChild(opt);
            });
        } else {
            const opt = document.createElement('option');
            opt.value = currentQty;
            opt.textContent = currentQty;
            sel.appendChild(opt);
        }
    }



  // === Actualizar totales ===
  function updateTotals(summary){
    const t = summary.totals || {};
    document.getElementById('subtotal').innerText = '$' + formatNumber(t.subtotal);
    document.getElementById('iva').innerText      = '$' + formatNumber(t.tax);
    document.getElementById('total').innerText    = '$' + formatNumber(t.total);
  }

  function formatNumber(n){ return (n || 0).toLocaleString('es-CL'); }

  // === Eventos ===
  function bindCartButtons(){
    // eliminar
    $('.js-del').off().on('click', async function(){
      const id = $(this).data('id');
      const ok = await Swal.fire({title:'¿Eliminar producto?',icon:'warning',showCancelButton:true});
      if(!ok.isConfirmed) return;
      await fetch(`/store/cart/remove/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN':csrf}});
      loadCart();
    });

    // actualizar cantidad
    $('.js-qty').off().on('change', async function(){
      const id = $(this).data('id');
      const qty = $(this).val();
      await fetch(`/store/cart/update/${id}`, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},
        body: JSON.stringify({qty})
      });
      loadCart();
    });
  }

  // === Continuar ===
  $('#btn-continue').on('click', async ()=>{
    const res = await fetch('/store/profile/check');
    const j = await res.json();
    if(j.logged){ window.location.href = '/store/checkout/step2'; }
    else { window.location.href = '/store/checkout/guest'; }
  });

  loadCart();
});
</script>
@endpush
