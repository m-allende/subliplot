
<!doctype html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','AGStore')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous">

  @vite(['resources/js/app.js']) {{-- app.js importa ../sass/app.scss y bootstrap --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

  </style>
  @stack('styles')
</head>
<body class="bg-body">
  <header class="site-header bg-black"> 
    {{-- Topbar --}}
    <div class="bg-black text-light small py-1 border-bottom border-secondary-subtle">
        <div class="container-xxl d-flex justify-content-between align-items-center">
        <div class="d-none d-md-block">Subliplot · +56 9 2942 1578 · alejandro@subliplot.cl</div>
        <div class="ms-auto d-flex align-items-center gap-3">
          @guest
            {{-- Invitado: mostrar Ingresar (offcanvas) y Crear cuenta --}}
            <a href="#" class="link-light text-decoration-none"
              data-bs-toggle="offcanvas" data-bs-target="#loginOffcanvas">
              <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
            </a>
            <a href="{{ route('register') }}" class="link-light text-decoration-none">
              <i class="bi bi-person-plus me-1"></i> Crear cuenta
            </a>
          @endguest

          @auth
            @php
              $u = Auth::user();
              $avatar = optional($u->photos->firstWhere('is_primary', true))->url ?? asset('img/no-image.jpg')
            @endphp
            <div class="dropdown">
              <a href="#" class="d-flex align-items-center text-decoration-none link-light dropdown-toggle"
                id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ $avatar }}" alt="avatar" class="rounded-circle me-2" width="32" height="32">
                <span class="d-none d-sm-inline">{{ Str::limit($u->name, 22) }}</span>
              </a>

              {{-- Menú cuenta (oscuro, con card encabezado) --}}
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow p-0"
                  aria-labelledby="accountDropdown" style="min-width: 260px">
                <div class="p-3 border-bottom border-secondary-subtle d-flex align-items-center">
                  <img src="{{ $avatar }}" alt="avatar" class="rounded-circle me-3" width="44" height="44">
                  <div class="small">
                    <div class="fw-semibold">{{ $u->name }}</div>
                    <div class="text-secondary">{{ $u->email }}</div>
                  </div>
                </div>

                @role('admin')
                  <a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Administrar sistema
                  </a>
                @endrole

                <a class="dropdown-item py-2" href="#">
                  <i class="bi bi-person me-2"></i> Perfil
                </a>

                <div class="dropdown-divider m-0"></div>

                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item py-2 text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                  </button>
                </form>
              </div>
            </div>
          @endauth
        </div>
    </div>

    {{-- Header --}}
    <div class=" container-xxl py-3 d-flex align-items-center gap-3 mb-0">
      <a href="#" class="navbar-brand d-flex align-items-center gap-2 m-0">
        <img src="{{ asset('images/subliplot.jpeg') }}" alt="AGStore" width="90" height="90" class="rounded-circle object-fit-cover border border-light-subtle">
      </a>
      <form class="flex-grow-1 d-none d-md-flex" role="search">
        <input class="form-control form-control-lg" type="search" placeholder="Buscar productos…" aria-label="Buscar">
      </form>
      <div class="ms-auto d-flex align-items-center gap-2">
        <button class="btn btn-outline-light d-md-none" data-bs-toggle="collapse" data-bs-target="#searchCollapse"><i class="bi bi-search"></i></button>
        <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas"><i class="bi bi-bag"></i> <span class="d-none d-lg-inline">Carrito</span></button>
      </div>
    </div>
    <div id="searchCollapse" class="container-xxl collapse pb-3">
      <form role="search">
        <input class="form-control" type="search" placeholder="Buscar productos…">
      </form>
    </div>

    {{-- Nav con dropdowns--}}
    <nav class="navbar navbar-expand-lg border-bottom border-secondary-subtle">
      <div class="container-xxl container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
          <ul class="navbar-nav w-100 d-flex">
            <li class="nav-item flex-fill text-center">
              <a class="nav-link {{ request()->routeIs('index')?'active':'' }}" href="{{ route('index') }}">
                <i class="bi bi-house me-1"></i>Inicio
              </a>
            </li>

            @foreach($navCategories ?? collect() as $cat)
              @php
                $catPhoto = optional($cat->photos->first());
                $products = $cat->products ?? collect();
              @endphp

              <li class="nav-item flex-fill text-center dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                  {{ $cat->name }}
                </a>

                {{-- Dropdown sólido, alto máx con scroll si hay muchos productos --}}
                <ul class="dropdown-menu dropdown-menu-dark shadow" style="max-height:60vh; overflow:auto; min-width:260px">
                  @forelse($products as $p)
                    @php
                      // FOTO mini del ítem del menú
                      $thumb = optional($p->photos->first())->url
                              ?? (optional($p->photos->first())->path ? asset(optional($p->photos->first())->path) : null);
                    @endphp

                    <li>
                      <a href="#"
                          class="dropdown-item d-flex align-items-center gap-2 js-open-product"
                          data-pid="{{ $p->id }}"
                          data-name="{{ $p->name }}"
                          data-sub="{{ $p->subtitle ?? $p->category->name ?? '' }}"
                          data-photos="{{ $p->photos->map(fn($ph)=> $ph->url ?? ($ph->path?asset($ph->path):null))->filter()->implode('|') }}"
                          data-uses-size="{{ (int)($p->uses_size ?? false) }}"
                          data-uses-paper="{{ (int)($p->uses_paper ?? false) }}"
                          data-uses-bleed="{{ (int)($p->uses_bleed ?? false) }}"
                          data-uses-finish="{{ (int)($p->uses_finish ?? false) }}"
                          data-uses-material="{{ (int)($p->uses_material ?? false) }}"
                          data-uses-shape="{{ (int)($p->uses_shape ?? false) }}"
                          data-uses-print_side="{{ (int)($p->uses_print_side ?? false) }}"
                          data-uses-mounting="{{ (int)($p->uses_mounting ?? false) }}"
                          data-uses-rolling="{{ (int)($p->uses_rolling ?? false) }}"
                          data-uses-holes="{{ (int)($p->uses_holes ?? false) }}"
                        >
                        @if($thumb)
                          <img src="{{ $thumb }}" class="rounded object-fit-cover" width="24" height="24" alt="">
                        @endif
                        <span>{{ $p->name }}</span>
                      </a>
                    </li>
                  @empty
                    <li><span class="dropdown-item text-secondary">Sin productos</span></li>
                  @endforelse
                </ul>

              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main>@yield('content')</main>

    {{-- Offcanvas Carrito (visual) --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" style="z-index:1070">
        <div class="offcanvas-header">
        <h5 class="offcanvas-title"><i class="bi bi-bag me-2"></i>Tu carrito</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
        <div class="vstack gap-3 flex-grow-1">
            {{-- Ítems de ejemplo --}}
            <div class="d-flex gap-3 align-items-start border-bottom border-secondary-subtle pb-3">
            <img src="https://picsum.photos/seed/p1/96/96" class="rounded object-fit-cover" width="72" height="72" alt="">
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                <strong>Sticker troquelado</strong>
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button>
                </div>
                <div class="text-secondary small">Cantidad: 2 · Var.: Mate</div>
                <div class="fw-semibold mt-1">$ 12.990</div>
            </div>
            </div>
            <div class="d-flex gap-3 align-items-start border-bottom border-secondary-subtle pb-3">
            <img src="https://picsum.photos/seed/p2/96/96" class="rounded object-fit-cover" width="72" height="72" alt="">
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                <strong>Pendón Roller 85x200</strong>
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button>
                </div>
                <div class="text-secondary small">Cantidad: 1</div>
                <div class="fw-semibold mt-1">$ 19.990</div>
            </div>
            </div>
        </div>
        <div class="mt-3 border-top border-secondary-subtle pt-3">
            <div class="d-flex justify-content-between"><span class="text-secondary">Subtotal</span><span>$ 32.980</span></div>
            <div class="d-flex justify-content-between"><span class="text-secondary">IVA (19%)</span><span>$ 6.266</span></div>
            <div class="d-flex justify-content-between fs-5 fw-bold mt-2"><span>Total</span><span>$ 39.246</span></div>
            <div class="d-grid mt-3">
            <a href="#" class="btn btn-primary btn-lg">Ir a pagar</a>
            </div>
        </div>
        </div>
    </div>

    {{-- Offcanvas LOGIN (nuevo) --}}
    <div class="offcanvas offcanvas-end" id="loginOffcanvas" tabindex="-1" style="z-index:1080">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title"><i class="bi bi-person-circle me-2"></i>Ingresar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <form method="POST" action="{{ route('login') }}" class="vstack gap-3">
          @csrf
          <div>
            <label class="form-label" for="loginEmail">Correo</label>
            <input type="email" name="email" id="loginEmail" class="form-control form-control-lg" required autofocus>
          </div>
          <div>
            <label class="form-label" for="loginPass">Contraseña</label>
            <input type="password" name="password" id="loginPass" class="form-control form-control-lg" required>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="remember">
              <label class="form-check-label" for="remember">Recordarme</label>
            </div>
            <a href="{{ route('password.request') }}" class="small link-light">¿Olvidaste tu contraseña?</a>
          </div>
          <button type="submit" class="btn btn-primary btn-lg w-100">Ingresar</button>
        </form>
      </div>
    </div>


    <footer class="bg-transparent border-top border-secondary-subtle mt-5">
        <div class="container-xxl py-4 d-flex flex-column flex-md-row gap-3 justify-content-between">
            <div><div class="fw-bold">Subliplot</div><div class="text-secondary small">Impresión y soluciones gráficas en Chile.</div></div>
            <div class="text-secondary small">© {{ date('Y') }} · Términos · Privacidad</div>
        </div>
    </footer>

    {{-- Modal único global --}}
    @include('store.partials.product-config-modal')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
          crossorigin="anonymous"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>


