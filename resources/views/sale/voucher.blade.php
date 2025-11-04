<style>
    h2,
    p,
    table,
    tr,
    td {
        padding: 0;
        margin: 0;
    }

    p,
    table,
    tr,
    td {
        font-size: 10;
    }
</style>
<div style="width: 58mm;padding: 0">
    <h2>Eben Ezer</h2>
    <p>Eben Ezer Automotriz SPA</p>
    <p>R.U.T 99.999.999-9</p>
    <p>Servicio Integral de Vehiculos</p>
    <p>Av San Gregorio 0160 - La Granja</p>
    <br>
    <p>Boleta Número: {{ $sale->number }} </p>
    <p>Fecha de Emisión: {{ date('d/m/Y', strtotime($sale->date)) }}</p>
    <hr>
    <table style="width: 100%">
        <tr>
            <td>Ctd.</td>
            <td>Descripción</td>
            <td>Precio</td>
            <td>Subt.</td>
        </tr>
        @foreach ($sale->products as $product)
            <tr>
                <td>{{ $product->pivot->quantity }} </td>
                <td>{{ $product->name }} </td>
                <td>{{ $product->pivot->price }} </td>
                <td>{{ $product->pivot->total }} </td>
            </tr>
        @endforeach
    </table>
    <hr>
    <h3>Total ${{ $sale->total }} </h3>
    <p>El iva de esta boleta es ${{ $sale->tax }} </p>

</div>
