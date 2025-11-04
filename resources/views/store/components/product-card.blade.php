@php
  // Normaliza $product
  $p = is_array($product) ? (object)$product : $product;

  // Foto principal (o fallback)
  $firstPhoto = optional(optional($p->photos)->first());
  $img = $firstPhoto?->url ?? ($firstPhoto?->path ? asset($firstPhoto->path) : asset('img/no-image.jpg'));

  // Todas las fotos para el carrusel del modal
  $photoUrls = collect($p->photos ?? [])
      ->map(fn($ph) => $ph->url ?? ($ph->path ? asset($ph->path) : null))
      ->filter()
      ->implode('|');

  // Flags de uso (si vienen null, los marco como 0)
  $f = fn($v) => (int) (!!$v);
@endphp

<article class="card glass-card h-100 border-0 shadow-sm hover-shadow">
  <div class="ratio ratio-1x1 overflow-hidden rounded-top-3">
    <img src="{{ $img }}" class="w-100 h-100 object-fit-cover" alt="{{ $p->name }}">
  </div>
  <div class="card-body d-flex flex-column text-light">
    <h3 class="h6 mb-1">{{ $p->name }}</h3>
    <p class="small text-secondary mb-3">{{ $p->subtitle ?? ($p->category->name ?? '') }}</p>

    <div class="mt-auto d-flex align-items-center justify-content-end gap-2">
      <button type="button"
         class="btn btn-primary js-open-product"
         data-pid="{{ $p->id }}"
         data-name="{{ $p->name }}"
         data-sub="{{ $p->subtitle ?? ($p->category->name ?? '') }}"
         data-photos="{{ $photoUrls }}"
         data-uses-size="{{ $f($p->uses_size ?? false) }}"
         data-uses-paper="{{ $f($p->uses_paper ?? false) }}"
         data-uses-bleed="{{ $f($p->uses_bleed ?? false) }}"
         data-uses-finish="{{ $f($p->uses_finish ?? false) }}"
         data-uses-material="{{ $f($p->uses_material ?? false) }}"
         data-uses-shape="{{ $f($p->uses_shape ?? false) }}"
         data-uses-print_side="{{ $f($p->uses_print_side ?? false) }}"
         data-uses-mounting="{{ $f($p->uses_mounting ?? false) }}"
         data-uses-rolling="{{ $f($p->uses_rolling ?? false) }}"
         data-uses-holes="{{ $f($p->uses_holes ?? false) }}">
        <i class="bi bi-eye me-1"></i> Ver producto
      </button>
    </div>
  </div>
</article>
