
@extends('layouts.storefront')
@section('title','Inicio')
@section('content')

  {{-- BLOQUE PDF: una sola card con texto + imagen --}}
  <section class="container-xxl pt-3 pt-md-4">
    <div class="glass-card rounded-4 border overflow-hidden">
      <div class="row g-0 align-items-stretch">
        <div class="col-12 col-lg-6 p-4 p-md-5">
          <div class="text-white" style="max-height: 560px; overflow:auto">
            <p>Desata tu creatividad y transforma tus ideas en realidades tangibles con la versatilidad incomparable de nuestra impresión digital. ¿Necesitas tarjetas de visita que dejen una impresión duradera, folletos informativos que cautiven a tu audiencia, impactantes carteles publicitarios que atraigan todas las miradas o incluso etiquetas personalizadas que realcen tus productos? En nuestra imprenta digital, te ofrecemos un universo de posibilidades para plasmar cada uno de tus proyectos con la precisión y calidad que merecen.</p>
            <p>Desde pequeñas tiradas personalizadas hasta grandes volúmenes, nos adaptamos a tus necesidades específicas, brindándote la flexibilidad de elegir materiales, acabados y formatos que se ajusten perfectamente a tu visión. Imagina poder personalizar cada detalle, desde el diseño hasta el tipo de papel, para crear piezas únicas y memorables.</p>
            <p>Te invitamos a navegar por nuestro menú y descubrir la amplia gama de productos y servicios de impresión digital que tenemos para ofrecerte. Explora nuestras opciones y encuentra la solución perfecta para comunicar tu mensaje de manera efectiva, promocionar tu negocio con impacto o simplemente dar vida a esas ideas creativas que tienes en mente. ¡Deja volar tu imaginación y nosotros nos encargamos de hacerla realidad!</p>
          </div>
        </div>
        <div class="col-12 col-lg-6">
          <div class="ratio ratio-4x3 h-100">
            <img src="{{ asset('images/hero-print.jpg') }}" class="w-100 h-100 object-fit-cover" alt="Impresión digital">
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Categorías --}}
  <section class="container-xxl py-4 py-md-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="h4 mb-0">Categorías</h2>
      <a href="#" class="link-primary">Ver todas <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-3 g-md-4">
      @forelse($featuredCategories as $cat)
        @php
          $photo = optional($cat->photos->first());
          $src   = $photo?->url ?? ($photo?->path ? asset($photo->path) : asset('img/no-image.jpg'));
        @endphp
        <div class="col-6 col-md-4 col-lg-3">
          <a href="#" class="text-decoration-none">
            <div class="card glass-card h-100 border-0 shadow-sm hover-shadow">
              <div class="ratio ratio-4x3 rounded-top-3 overflow-hidden position-relative">
                <img src="{{ $src }}" class="w-100 h-100 object-fit-cover" alt="{{ $cat->name }}">
              </div>
              <div class="card-body text-light">
                <h3 class="h6 mb-1">{{ $cat->name }}</h3>
                <p class="small text-secondary mb-0">{{ $cat->description ?? 'Explorar productos' }}</p>
              </div>
            </div>
          </a>
        </div>
      @empty
        <div class="col-12"><div class="alert alert-secondary">Aún no hay categorías.</div></div>
      @endforelse
    </div>

  </section>

  {{-- Destacados --}}
  <section class="container-xxl py-4 py-md-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="h4 mb-0">Destacados</h2>
      <a href="#" class="link-primary">Ver catálogo <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-3 g-md-4">
      @foreach($featuredProducts as $product)
        <div class="col-6 col-md-4 col-lg-3">
          @include('store.partials.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>
  </section>

  {{-- CTA final --}}
  <section class="container-xxl pb-5">
    <div class="p-4 p-md-5 rounded-4 glass-card d-flex flex-column flex-md-row align-items-center gap-3 gap-md-4">
      <div class="flex-grow-1">
        <h3 class="h4 mb-1">¿Trabajo especial?</h3>
        <p class="mb-0 text-secondary">Cotiza diseños y formatos personalizados: te asesoramos en materiales y acabados.</p>
      </div>
      <a href="#" class="btn btn-primary btn-lg"><i class="bi bi-chat-left-text me-1"></i> Contáctanos</a>
    </div>
  </section>
@endsection


@push('scripts')
<!-- jQuery (necesario para Select2, inputmask, etc.) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>

<!-- Select2 (opcional, sólo si lo vas a usar ahora) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<script>
(function () {
  const modalEl = document.getElementById('productConfigModal');
  const pcModal = new bootstrap.Modal(modalEl);

  const titleEl  = modalEl.querySelector('#pc_title');
  const subEl    = modalEl.querySelector('#pc_subtitle');
  const carInner = modalEl.querySelector('#pc_carousel .carousel-inner');
  const fileIn   = modalEl.querySelector('#pc_file');
  const fileLbl  = modalEl.querySelector('#pc_filename');
  const totalEl  = modalEl.querySelector('#pc_total');

  // Filas
  const rows = {
    size:        modalEl.querySelector('#row_size'),
    paper:       modalEl.querySelector('#row_paper'),
    bleed:       modalEl.querySelector('#row_bleed'),
    finish:      modalEl.querySelector('#row_finish'),
    material:    modalEl.querySelector('#row_material'),
    shape:       modalEl.querySelector('#row_shape'),
    print_side:  modalEl.querySelector('#row_print_side'),
    mounting:    modalEl.querySelector('#row_mounting'),
    rolling:     modalEl.querySelector('#row_rolling'),
    holes:       modalEl.querySelector('#row_holes'),
  };

  // Selects
  const selects = {
    size:        $(modalEl).find('#pc_size'),
    paper:       $(modalEl).find('#pc_paper'),
    bleed:       $(modalEl).find('#pc_bleed'),
    finish:      $(modalEl).find('#pc_finish'),
    material:    $(modalEl).find('#pc_material'),
    shape:       $(modalEl).find('#pc_shape'),
    print_side:  $(modalEl).find('#pc_print_side'),
    mounting:    $(modalEl).find('#pc_mounting'),
    rolling:     $(modalEl).find('#pc_rolling'),
    holes:       $(modalEl).find('#pc_holes'),
  };

  // Inicia Select2 una sola vez
  function initSelect2Once() {
    Object.values(selects).forEach($el => {
      if (!$el.data('select2')) {
        $el.select2({
          placeholder: $el.data('placeholder') || 'Seleccione…',
          dropdownParent: $(modalEl),
          width: '100%'
        });
      }
    });
  }
  initSelect2Once();

  // Helpers
  function toggle(el, show) { el.classList.toggle('d-none', !show); }
  function resetForm() {
    modalEl.querySelector('#pc_qty').value = 1;
    modalEl.querySelector('#pc_notes').value = '';
    Object.values(selects).forEach($el => $el.val(null).trigger('change'));
    fileIn.value = ''; fileLbl.textContent = 'No se eligió ningún archivo';
    totalEl.value = '0.000';
  }
  function fillCarousel(urls=[]) {
    carInner.innerHTML = '';
    if (!urls.length) urls = ['{{ asset('img/no-image.jpg') }}'];
    urls.forEach((u, idx) => {
      const item = document.createElement('div');
      item.className = 'carousel-item' + (idx===0 ? ' active' : '');
      item.innerHTML = `<img src="${u}" class="d-block w-100 object-fit-cover" style="max-height:520px">`;
      carInner.appendChild(item);
    });
  }
  function setSelectOptions($el, items=[]) {
    // Limpia y agrega una opción vacía
    $el.empty().append(new Option('', '', false, false));
    items.forEach(txt => $el.append(new Option(txt, txt, false, false)));
    $el.trigger('change');
  }

  // Archivo seleccionado
  fileIn.addEventListener('change', () => {
    fileLbl.textContent = fileIn.files?.[0]?.name || 'No se eligió ningún archivo';
  });

  // Abrir modal desde navbar / cards
  document.addEventListener('click', (e) => {
    const a = e.target.closest('.js-open-product');
    if (!a) return;
    e.preventDefault();

    // Datos básicos
    const name   = a.dataset.name   || 'Producto';
    const sub    = a.dataset.sub    || '';
    const photos = (a.dataset.photos || '').split('|').filter(Boolean);

    // Flags uses_*
    const flags = {
      size:        a.dataset.usesSize        === '1',
      paper:       a.dataset.usesPaper       === '1',
      bleed:       a.dataset.usesBleed       === '1',
      finish:      a.dataset.usesFinish      === '1',
      material:    a.dataset.usesMaterial    === '1',
      shape:       a.dataset.usesShape       === '1',
      print_side:  a.dataset.usesPrint_side  === '1',
      mounting:    a.dataset.usesMounting    === '1',
      rolling:     a.dataset.usesRolling     === '1',
      holes:       a.dataset.usesHoles       === '1',
    };

    // Mostrar / ocultar filas
    Object.entries(flags).forEach(([k, v]) => toggle(rows[k], v));

    // (Temporal) deja una opción placeholder “— Seleccionar —”
    Object.entries(selects).forEach(([k, $el]) => {
      if (flags[k]) setSelectOptions($el, []); // vacío por ahora
    });

    // Render básicos
    titleEl.textContent = name;
    subEl.textContent   = sub;
    fillCarousel(photos);
    resetForm();

    pcModal.show();
  });

  // “Agregar al carrito” demo
  modalEl.querySelector('#pc_add').addEventListener('click', () => {
    const el = document.createElement('div');
    el.className = 'toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3';
    el.innerHTML = '<div class="d-flex"><div class="toast-body">Producto agregado (maqueta).</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
    document.body.appendChild(el);
    bootstrap.Toast.getOrCreateInstance(el).show();
  });
})();
</script>

<script>
(function () {
  const modalEl = document.getElementById('productConfigModal');
  const pcModal = new bootstrap.Modal(modalEl);
  const carInner = modalEl.querySelector('#pc_carousel .carousel-inner');
  const titleEl  = modalEl.querySelector('#pc_title');
  const subEl    = modalEl.querySelector('#pc_subtitle');
  const fileIn   = modalEl.querySelector('#pc_file');
  const fileLbl  = modalEl.querySelector('#pc_filename');
  const totalEl  = modalEl.querySelector('#pc_total');
  const dynBox   = modalEl.querySelector('#pc_dynamic'); // << contenedor dinámico

  // Render carousel
  function fillCarousel(urls=[]) {
    carInner.innerHTML = '';
    if (!urls.length) urls = ['{{ asset('img/no-image.jpg') }}'];
    urls.forEach((u, idx) => {
      const item = document.createElement('div');
      item.className = 'carousel-item' + (idx===0 ? ' active' : '');
      item.innerHTML = `<img src="${u}" class="d-block w-100 object-fit-cover" style="max-height:520px">`;
      carInner.appendChild(item);
    });
  }

  // Construir un grupo (label + select)
  function buildSelectGroup(group) {
    const wrap = document.createElement('div');
    wrap.className = 'd-flex align-items-center gap-3';
    const label = document.createElement('div');
    label.className = 'fw-semibold';
    label.style.minWidth = '140px';
    label.textContent = group.name;
    const field = document.createElement('div');
    field.className = 'flex-grow-1';

    const select = document.createElement('select');
    select.className = 'form-select';
    select.setAttribute('data-code', group.code);
    if (group.multi) select.setAttribute('multiple', 'multiple');

    // placeholder
    const ph = document.createElement('option');
    ph.value = '';
    ph.textContent = group.placeholder || 'Seleccione...';
    if (!group.multi) select.appendChild(ph);

    // opciones
    (group.options || []).forEach(o => {
      const opt = document.createElement('option');
      opt.value = o.id;
      opt.textContent = o.text;
      select.appendChild(opt);
    });

    field.appendChild(select);
    wrap.appendChild(label);
    wrap.appendChild(field);

    // preselección
    if (Array.isArray(group.selected) && group.selected.length) {
      if (group.multi) {
        group.selected.forEach(v => {
          const opt = [...select.options].find(op => op.value == v);
          if (opt) opt.selected = true;
        });
      } else {
        const first = group.selected[0];
        const opt = [...select.options].find(op => op.value == first);
        if (opt) opt.selected = true;
      }
    }

    // si usas Select2, inicialízalo aquí (asegúrate de tenerlo cargado en storefront)
    if (window.jQuery && jQuery.fn.select2) {
      jQuery(select).select2({
        width: '100%',
        dropdownParent: jQuery(modalEl),
        placeholder: group.placeholder || 'Seleccione...',
      });
    }

    return wrap;
  }

  // Reset de formulario modal
  function resetModal() {
    dynBox.innerHTML = '';
    modalEl.querySelector('#pc_qty').value = 1;
    modalEl.querySelector('#pc_notes').value = '';
    fileIn.value = '';
    fileLbl.textContent = 'No se eligió ningún archivo';
    totalEl.value = '0.000';
  }

  // Archivo seleccionado (label)
  fileIn.addEventListener('change', () => {
    fileLbl.textContent = fileIn.files?.[0]?.name || 'No se eligió ningún archivo';
  });

  // Abrir modal desde navbar/cards
  document.addEventListener('click', async (e) => {
    const a = e.target.closest('.js-open-product');
    if (!a) return;
    e.preventDefault();

    const pid   = a.dataset.pid;
    const name  = a.dataset.name || 'Producto';
    const sub   = a.dataset.sub  || '';

    resetModal();
    titleEl.textContent = name;
    subEl.textContent   = sub;

    // Cargar configuración (atributos habilitados + opciones + preselección + fotos)
    try {
      const res = await fetch(`/store/products/${pid}/config`, { headers: {'X-Requested-With':'XMLHttpRequest'} });
      const json = await res.json();

      fillCarousel(json.photos || []);

      // construir grupos
      (json.groups || []).forEach(g => {
        dynBox.appendChild( buildSelectGroup(g) );
      });

      pcModal.show();
    } catch (err) {
      console.error(err);
      alert('No se pudo cargar la configuración del producto.');
    }
  });

  // Botón “Agregar al carrito” (placeholder)
  modalEl.querySelector('#pc_add').addEventListener('click', () => {
    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3';
    toastEl.innerHTML = '<div class="d-flex"><div class="toast-body">Producto agregado (maqueta).</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
    document.body.appendChild(toastEl);
    bootstrap.Toast.getOrCreateInstance(toastEl).show();
  });

})();
</script>

@endpush


