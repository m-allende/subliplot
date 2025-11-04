<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #ccc; padding:6px; }
    .right { text-align:right; }
    .center { text-align:center; }
  </style>
</head>
<body>
  <h2 class="center">{{ strtoupper($doc->type) }} @if($doc->folio) N° {{ $doc->folio }} @endif</h2>

  <p><strong>Orden:</strong> #{{ $order->id }} &nbsp; <strong>Fecha:</strong> {{ $doc->issued_at?->format('d-m-Y H:i') }}</p>

  <p>
    <strong>Receptor:</strong>
    {{ $doc->receiver_name ?? ($order->user->name ?? 'Consumidor final') }}<br>
    @if($doc->receiver_rut) <strong>RUT:</strong> {{ $doc->receiver_rut }}<br>@endif
    @if($doc->receiver_giro) <strong>Giro:</strong> {{ $doc->receiver_giro }}<br>@endif
    @if($doc->receiver_address) <strong>Dirección:</strong> {{ $doc->receiver_address }} @endif
  </p>

  <table>
    <thead>
      <tr>
        <th>Producto</th><th class="right">Cantidad</th><th class="right">Unitario</th><th class="right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $it)
        <tr>
          <td>
            {{ $it->product_name }}

            @php
              // soporta array (cast) o string json
              $opts = is_array($it->options_display)
                    ? $it->options_display
                    : (json_decode($it->options_display ?? '[]', true) ?: []);
            @endphp

            @if(!empty($opts))
              <div style="font-size:11px;color:#666">
                @foreach($opts as $op)
                  <div>{{ $op['group'] ?? '' }}: {{ $op['value'] ?? '' }}</div>
                @endforeach
              </div>
            @endif

          </td>
          <td class="right">
            {{ is_numeric($it->qty_display ?? null) ? $it->qty_display : ($it->qty_real ?? $it->qty_raw) }}
          </td>
          <td class="right">$ {{ number_format($it->unit_price_gross,0,',','.') }}</td>
          <td class="right">$ {{ number_format($it->line_total_gross,0,',','.') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <p class="right">
    Subtotal: $ {{ number_format($doc->subtotal_net,0,',','.') }}<br>
    IVA (19%): $ {{ number_format($doc->tax_total,0,',','.') }}<br>
    <strong>Total: $ {{ number_format($doc->grand_total,0,',','.') }}</strong>
  </p>
</body>
</html>
