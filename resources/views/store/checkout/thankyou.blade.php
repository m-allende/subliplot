@extends('store.layouts.app')

@section('title','Orden recibida')

@section('content')
<div class="container py-5 text-light">
  <div class="card glass-card border-0 p-4">

    {{-- Encabezado éxito --}}
    <div class="d-flex align-items-start gap-3 mb-3">
      <div class="rounded-circle bg-success bg-opacity-25 text-success d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px">
        <i class="bi bi-check2 fs-4"></i>
      </div>
      <div>
        <h4 class="mb-1">¡Gracias! Hemos recibido tu pedido</h4>
        <div class="text-secondary">
          N° Orden: <strong>#{{ $order->id }}</strong>
          · Total: <strong>${{ number_format($order->grand_total,0,',','.') }}</strong>
        </div>
      </div>
    </div>

    {{-- Acciones principales --}}
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
      <a href="{{ route('index') }}" class="btn btn-outline-light">
        <i class="bi bi-arrow-left-short me-1"></i> Volver a la tienda
      </a>

      @if(!empty($pdfUrl))
        <a href="{{ $pdfUrl }}" target="_blank" class="btn btn-success shadow-sm">
          <i class="bi bi-download me-1"></i>
          Descargar {{ strtoupper($doc->type ?? 'boleta') }}
        </a>
      @endif
    </div>

    <hr class="border-secondary my-4">

    {{-- Detalle items --}}
    <h6 class="mb-3">Detalle</h6>
    <ul class="list-unstyled mb-0">
      @foreach($order->items as $it)
        <li class="mb-3 d-flex align-items-center gap-3">
          @if($it->product_thumb)
            <img src="{{ $it->product_thumb }}" width="56" height="56" class="rounded object-fit-cover border border-secondary-subtle">
          @endif

          <div class="flex-grow-1">
            <div class="fw-semibold">{{ $it->product_name }}</div>
            @php
              $opts = is_array($it->options_display)
                    ? $it->options_display
                    : (json_decode($it->options_display ?? '[]', true) ?: []);
            @endphp
            @if(!empty($opts))
              <div class="small text-secondary">
                @foreach($opts as $o)
                  <div>{{ $o['group'] ?? '' }}: {{ $o['value'] ?? '' }}</div>
                @endforeach
              </div>
            @endif
          </div>

          @if($it->files && $it->files->count())
            <div class="small mt-2">
              <b>Adjuntos:</b>
              @foreach($it->files as $f)
                <a href="{{ Storage::disk('public_uploads')->url($f->path) }}" target="_blank" class="d-inline-flex align-items-center text-decoration-none text-light me-2">
                  @if(Str::startsWith($f->mime,'image/'))
                    <img src="{{ Storage::disk('public_uploads')->url($f->path) }}" width="48" height="48" class="rounded me-1 border border-secondary-subtle" style="object-fit:cover">
                  @else
                    <i class="bi bi-paperclip me-1"></i>
                  @endif
                  {{ $f->original_name ?? 'Archivo' }}
                </a>
              @endforeach
            </div>
          @endif


          <div class="text-end">
            <div class="small text-secondary">
              Cant: {{ is_numeric($it->qty_display ?? null) ? $it->qty_display : ($it->qty_real ?? $it->qty_raw) }}
            </div>
            <div class="fw-bold">
              ${{ number_format($it->line_total_gross,0,',','.') }}
            </div>
          </div>
        </li>
      @endforeach
    </ul>

  </div>
</div>
@endsection
