@extends('store.layouts.app')

@section('content')
<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="mb-3">
        {{ $ok ? '🎉 Pago aprobado' : '❌ Pago rechazado' }}
      </h3>
      <p class="text-muted">{{ $message }}</p>

      @if($details)
        <ul class="small">
          <li><strong>buyOrder:</strong> {{ $details->buy_order }}</li>
          <li><strong>status:</strong> {{ $details->status }}</li>
          <li><strong>amount:</strong> {{ $details->amount }}</li>
          <li><strong>authorization_code:</strong> {{ $details->authorization_code }}</li>
          <li><strong>payment_type_code:</strong> {{ $details->payment_type_code }}</li>
          <li><strong>installments_number:</strong> {{ $details->installments_number }}</li>
        </ul>
      @endif

      <a href="{{ url('/') }}" class="btn btn-primary">Volver al inicio</a>
    </div>
  </div>
</div>
@endsection
