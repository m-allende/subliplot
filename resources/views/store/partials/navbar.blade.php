<header class="site-header bg-black"> 
  {{-- Topbar (siempre visible) --}}
  <div class="bg-black text-light small py-1 border-bottom border-secondary-subtle">
    <div class="container-xxl d-flex justify-content-between align-items-center">
      <div class="d-none d-md-block">Subliplot · +56 9 2942 1578 · alejandro@subliplot.cl</div>
      <div class="ms-auto d-flex align-items-center gap-3">
        @guest
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

              <a class="dropdown-item py-2" href="{{ route('store.profile.index') }}">
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
  </div>

  {{-- OCULTAR TODO ESTO EN CHECKOUT --}}
  @unless (Request::is('store/checkout*'))
    {{-- Header --}}
    <div class="container-xxl py-3 d-flex align-items-center gap-3 mb-0">
      <a href="#" class="navbar-brand d-flex align-items-center gap-2 m-0">
        <img src="{{ asset('images/subliplot.jpeg') }}" alt="AGStore" width="90" height="90" class="rounded-circle object-fit-cover border border-light-subtle">
      </a>
      <form class="flex-grow-1 d-none d-md-flex" role="search">
        <input class="form-control form-control-lg" type="search" placeholder="Buscar productos…" aria-label="Buscar">
      </form>
      <div class="ms-auto d-flex align-items-center gap-2">
        <button class="btn btn-outline-light d-md-none" data-bs-toggle="collapse" data-bs-target="#searchCollapse"><i class="bi bi-search"></i></button>
        <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
          <span class="position-relative d-inline-flex align-items-center">
            <i class="bi bi-bag fs-5"></i>
            <span id="cartCount" class="cart-badge">0</span>
          </span>
          <span class="d-none d-lg-inline">Carrito</span>
        </button>
      </div>
    </div>

    <div id="searchCollapse" class="container-xxl collapse pb-3">
      <form role="search">
        <input class="form-control" type="search" placeholder="Buscar productos…">
      </form>
    </div>

    {{-- Nav con dropdowns --}}
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

                <ul class="dropdown-menu dropdown-menu-dark shadow" style="max-height:60vh; overflow:auto; min-width:260px">
                  @forelse($products as $p)
                    @php
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
                         data-uses-quantity="{{ (int)($p->uses_quantity ?? false) }}">
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
  @endunless
</header>
