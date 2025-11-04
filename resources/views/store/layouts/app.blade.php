<!doctype html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Subliplot')</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-32x32.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">
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
    <style>
    /* Fondo negro con difuminado */
    body.bg-body {
        background-color: black;
        background-image:
            linear-gradient(
                to top right,
                black, #2b2a2aff
            );
    }
    .object-fit-cover{object-fit:cover}
    .hover-shadow:hover{box-shadow:0 .5rem 1rem rgba(0,0,0,.35)!important}

    /* Glass cards */
    .glass-card{background:rgba(255,255,255,.06)!important;border:1px solid rgba(255,255,255,.12)!important;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px)}
    .{background:rgba(255,255,255,.04)!important;border:1px solid rgba(255,255,255,.10)!important;backdrop-filter:blur(4px)}

    /* Evitar cajas blancas en .ratio */
    .ratio{background:transparent}
    .ratio > img{display:block;background:transparent}

    /* Inputs oscuros */
    .form-control,.form-select{background-color:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:#fff}
    .form-control::placeholder{color:rgba(255,255,255,.6)}

    /* Header fijo con alto z-index */
    .site-header{position:sticky; top:0; z-index:1060; backdrop-filter:blur(4px)}

    /* Navbar estilo Bootstrap + dropdowns sólidos y por delante */
    .navbar .nav-link{color:#fff}
    .navbar .nav-link:hover{color:#0d6efd}
    .navbar .dropdown-menu{
      --bs-dropdown-bg:#161617; /* sólido, no transparente */
      --bs-dropdown-border-color:rgba(255,255,255,.12);
      z-index: 2000; /* por delante de cards */
    }

    /* Siempre por delante del header/dropdowns */
    :root { --zl-modal: 3500; }
    .modal            { z-index: var(--zl-modal) !important; }
    .modal-backdrop   { z-index: calc(var(--zl-modal) - 1) !important; }

    /* Evita que los dropdowns pisen el modal (si los dejaste con z-index alto) */
    .navbar .dropdown-menu { z-index: 1040 !important; }

    .cart-badge{
      position:absolute; top:-6px; right:-10px;
      display:none;              /* oculto si 0 */
      min-width:18px; height:18px; padding:0 5px;
      border-radius:999px; font-size:.70rem; line-height:18px;
      background:#0d6efd; color:#fff; text-align:center;
      box-shadow:0 0 0 2px rgba(255,255,255,.85); /* halo blanco sutil */
    }

  </style>
  @stack('styles')
</head>
<body class="bg-dark">

  @include('store.partials.navbar')

  <main class="min-vh-60">
    @yield('content')
  </main>

  @include('store.partials.footer')

  {{-- MODALS GLOBALES (disponibles en todas las páginas) --}}
  @include('store.partials.modals.product-config')

  @stack('modals')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
          crossorigin="anonymous"></script>
    <script>
      window.STORE_ROUTES = {
        cartAdd:    "/store/cart/add",
        cartSummary:"/store/cart/summary",
        cartRemove: "/store/cart/remove/:rowId", // usar replace(':rowId', id) en JS
        productConfig: "/store/products/:pid/config", // idem replace
        cartClear:"{{ route('store.cart.clear') }}"
      };
      window.CSRF_TOKEN = "{{ csrf_token() }}";
    </script>
    
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    {{-- Cropper.js --}}
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script src="{{ asset('/js/product-config.js')}}"></script>

  @stack('scripts')
</body>
</html>
