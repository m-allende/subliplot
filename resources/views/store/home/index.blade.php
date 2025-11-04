@extends('store.layouts.app')
@section('title','Inicio')

@section('content')
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

  <section class="container-xxl pb-5 mt-3">
    <div class="p-4 p-md-5 rounded-4 glass-card d-flex flex-column flex-md-row align-items-center gap-3 gap-md-4">
      <div class="flex-grow-1">
        <h3 class="h4 mb-1">¿Trabajo especial?</h3>
        <p class="mb-0 text-secondary">Cotiza diseños y formatos personalizados: te asesoramos en materiales y acabados.</p>
      </div>
      <a href="{{ route('whatsapp.contact') }}" class="btn btn-primary btn-lg" target="_blank">
        <i class="bi bi-whatsapp me-1"></i> Contáctanos
      </a>

    </div>
  </section>

  <!--
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

  <section class="container-xxl py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="h4 text-white mb-0">Destacados</h2>
      <a href="{{ route('catalog') }}" class="link-primary">Ver catálogo</a>
    </div>
    <div class="row g-3">
      @foreach($featuredProducts as $product)
        <div class="col-6 col-md-4 col-lg-3">
          @include('store.components.product-card', ['product'=>$product])
        </div>
      @endforeach
    </div>
  </section>
-->
@endsection
