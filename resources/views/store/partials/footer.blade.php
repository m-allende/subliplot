{{-- Offcanvas Carrito (visual) --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" style="z-index:1070">
  <div class="offcanvas-header d-flex justify-content-between align-items-center mb-2">
    <h5 class="offcanvas-title"><i class="bi bi-bag me-2"></i>Tu carrito</h5>
    <div class="d-flex gap-2">
      <button id="cartClear" class="btn btn-sm btn-outline-secondary">Vaciar</button>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
  </div>

  <div class="offcanvas-body d-flex flex-column">

    <!-- LISTA -->
    <div id="cartItems" class="vstack gap-3 flex-grow-1">
      <!-- Se rellena por JS -->
    </div>

    <!-- TOTALES -->
    <div class="mt-3 border-top pt-3">
      <div class="d-flex justify-content-between">
        <span class="text-secondary">Subtotal</span>
        <span id="cartSubtotal">$ 0</span>
      </div>
      <div class="d-flex justify-content-between">
        <span class="text-secondary">IVA (19%)</span>
        <span id="cartTax">$ 0</span>
      </div>
      <div class="d-flex justify-content-between fs-5 fw-bold mt-2">
        <span>Total</span>
        <span id="cartTotal">$ 0</span>
      </div>
      <div class="d-grid mt-3">
        <a id="cartCheckout" href="#" class="btn btn-primary btn-lg">Ir a pagar</a>
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