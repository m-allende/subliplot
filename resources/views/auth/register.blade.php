{{-- @extends('store.layout') --}}
@extends('store.layouts.app') {{-- Ajusta al layout real de la tienda --}}

@section('title', 'Crear cuenta')

@section('content')
<section class="container-xxl py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
          <h1 class="h4 mb-3 text-center">Crear cuenta</h1>
          <p class="text-center text-muted mb-4">
            Regístrate para comprar más rápido, ver tu historial y guardar tus configuraciones.
          </p>

          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $e)
                  <li>{{ $e }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
          @endif

          <form method="POST" action="" novalidate id="formUser">
            @csrf

            <div class="mb-3">
              <label class="form-label">Nombre completo</label>
              <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Correo electrónico</label>
              <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" class="form-control" required>
              </div>
            </div>

            <div class="form-check my-3">
              <input class="form-check-input" type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
              <label class="form-check-label" for="terms">
                Acepto los <a href="{{ url('/terminos') }}" target="_blank">términos y condiciones</a>.
              </label>
            </div>

            <button type="button" class="btn btn-primary btn-lg w-100" id="btn-create">Crear cuenta</button>

            <p class="text-center mt-3 mb-0">
                ¿Ya tienes cuenta?
                <a href="#" class="link-light text-decoration-none" data-bs-toggle="offcanvas" data-bs-target="#loginOffcanvas">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                </a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
@push('scripts')
<script>
    $(function(){
        const $formUser  = $('#formUser');
        $('#btn-create').on('click', function(e){
            e.preventDefault();
            const data = $formUser.serializeArray();
            $.post("{{ route('userregister.store') }}", $.param(data), function(res){
                if (res.status == 200){ 
                    window.location.href = "{{ route('index') }}";
                }else { 
                    showErrors(res);
                }
            }).fail(()=>errorToast("Error al Grabar"));
        });

        function showErrors(res){
            let error = '';
            if (res.errors){ $.each(res.errors, (k,v)=>{ error += (Array.isArray(v)? v.join('<br>'): v) + '<br>'; }); }
            Swal.fire({ icon:'error', title:'Error', html: error || 'Error al procesar' });
        }
    });
</script>
@endpush