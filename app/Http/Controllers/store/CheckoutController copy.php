<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\OrderDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    public function index() { 
        return view('store.checkout.index'); 
    }

    public function step2() { 
        return view('store.checkout.step2'); 
    }

    public function step3() { 
        return view('store.checkout.step3'); 
    }

    public function guest() { return view('store.checkout.step2'); } // usa misma vista adaptada

    public function guestSave(Request $request)
    {
        // aquí podrías guardar datos temporales en sesión o tabla guest_orders
        session(['guest_checkout' => $request->all()]);
        return response()->json(['status'=>200,'message'=>'Datos de invitado guardados']);
    }

    public function place(Request $request)
    {
        // 1) Validar inputs, condicionados por doc_type
        $docType = $request->input('doc_type', 'boleta'); // 'boleta' | 'factura'
        $rules = [
            'doc_type' => 'required|in:boleta,factura',

            // dirección de envío elegida o datos (si permites libre)
            'shipping_address_id' => 'nullable|integer',
            'shipping_line1'      => 'nullable|string|max:255',
            'shipping_line2'      => 'nullable|string|max:255',
            'shipping_reference'  => 'nullable|string|max:255',
            'shipping_country_id' => 'nullable|integer',
            'shipping_region_id'  => 'nullable|integer',
            'shipping_commune_id' => 'nullable|integer',
        ];
        if ($docType === 'factura') {
            $rules = array_merge($rules, [
                'receiver_rut'     => 'required|string|max:20',
                'receiver_name'    => 'required|string|max:255',
                'receiver_giro'    => 'required|string|max:255',
                'receiver_address' => 'required|string|max:255',
                'receiver_country_id' => 'required|integer',
                'receiver_region_id'  => 'required|integer',
                'receiver_commune_id' => 'required|integer',
            ]);
        }
        $data = $request->validate($rules);

        // 2) Intentar hasta 3 veces (transacción) para grabar orden + documento
        $attempts = 0;
        $lastError = null;

        while ($attempts < 3) {
            $attempts++;
            try {
                return DB::transaction(function () use ($docType, $data) {
                    // ==== Obtener resumen del carrito desde tu sesión o helper actual ====
                    // Debes tener algo como makeSummary(session('cart'))
                    $cart = session('cart');
                    if (empty($cart['items'])) {
                        return response()->json(['status'=>400,'message'=>'Carrito vacío.'], 400);
                    }
                    $summary = $this->makeSummaryLikeCart($cart); // implementa igual que tu CartController->makeSummary

                    // ==== Crear Order ====
                    $order = Order::create([
                        'user_id'        => Auth::id(),
                        'public_uid'     => (string) Str::uuid(),
                        'status'         => 'pending',
                        'payment_status' => 'unpaid',
                        'doc_type'       => $docType,
                        'subtotal_net'   => $summary['totals']['subtotal'],
                        'tax_total'      => $summary['totals']['tax'],
                        'grand_total'    => $summary['totals']['total'],
                        'currency'       => $summary['currency'] ?? 'CLP',
                        'notes'          => null,
                        'channel'        => 'web',
                    ]);

                    // ==== Items ====
                    foreach ($summary['items'] as $it) {
                        OrderItem::create([
                            'order_id'           => $order->id,
                            'product_id'         => $it['product']['id'] ?? null,
                            'product_name'       => $it['product']['name'] ?? 'Producto',
                            'product_thumb'      => $it['thumb'] ?? null,
                            'qty_raw'            => (int) preg_replace('/\D/','', (string)($it['qty'] ?? 1)),
                            'qty_real'           => (int) preg_replace('/\D/','', (string)($it['qty'] ?? 1)),
                            'qty_display'        => (string) ($it['qty'] ?? 1),
                            'unit_price_gross'   => (int) ($it['unit'] ?? 0),
                            'line_total_gross'   => (int) ($it['line_net'] ?? 0),
                            'tax_rate'           => 19,
                            'options_json'       => json_encode($it['options_display'] ?? [], JSON_UNESCAPED_UNICODE),
                        ]);
                    }

                    // ==== Dirección de envío (si corresponde) ====
                    if (!empty($data['shipping_address_id'])) {
                        // si vienes desde dirección del usuario, cópiala a la orden (desnormalizado)
                        // ... recupera Address y copia sus campos:
                        $this->copyShippingFromAddressId($order, (int)$data['shipping_address_id']);
                    } elseif (!empty($data['shipping_line1'])) {
                        OrderAddress::create([
                            'order_id'     => $order->id,
                            'type'         => 'shipping',
                            'line1'        => $data['shipping_line1'] ?? null,
                            'line2'        => $data['shipping_line2'] ?? null,
                            'reference'    => $data['shipping_reference'] ?? null,
                            'country_id'   => $data['shipping_country_id'] ?? null,
                            'region_id'    => $data['shipping_region_id'] ?? null,
                            'commune_id'   => $data['shipping_commune_id'] ?? null,
                            'postal_code'  => null,
                            'latitude'     => null,
                            'longitude'    => null,
                            'country_name' => null,
                            'region_name'  => null,
                            'commune_name' => null,
                        ]);
                    }

                    // ==== Documento tributario (boleta/factura) ====
                    $doc = OrderDocument::create([
                        'order_id' => $order->id,
                        'type'     => $docType,
                        'status'   => 'pending',
                        'receiver_rut'     => $data['receiver_rut']     ?? null,
                        'receiver_name'    => $data['receiver_name']    ?? null,
                        'receiver_giro'    => $data['receiver_giro']    ?? null,
                        'receiver_address' => $data['receiver_address'] ?? null,
                        'receiver_country_id' => $data['receiver_country_id'] ?? null,
                        'receiver_region_id'  => $data['receiver_region_id']  ?? null,
                        'receiver_commune_id' => $data['receiver_commune_id'] ?? null,
                        'subtotal_net' => $summary['totals']['subtotal'],
                        'tax_total'    => $summary['totals']['tax'],
                        'grand_total'  => $summary['totals']['total'],
                        'currency'     => $summary['currency'] ?? 'CLP',
                    ]);

                    // ==== Generar PDF (esqueleto) ====
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.documents.tax', [
                        'order' => $order->fresh(['items']),
                        'doc'   => $doc,
                    ]);

                    // Usa el MISMO disk que usas para usuarios/productos/categorías
                    $disk = 'public_uploads'; // <— tu disk custom
                    $dir  = 'documents/orders/'; // estructura hermanada a tus fotos
                    $filename = 'document_'.$order->id."_".$doc->type.'_'.$doc->id.'.pdf';
                    $path = $dir.'/'.$filename; // ruta relativa en el disk

                    \Storage::disk($disk)->makeDirectory($dir);
                    \Storage::disk($disk)->put($path, $pdf->output());

                    $doc->update([
                        'pdf_path'  => $path,
                        'status'    => 'issued',
                        'issued_at' => now(),
                        // 'folio' => ... si luego integras folios oficiales/SII
                    ]);

                    // (opcional) marcar orden como “confirmed” si no usas pago
                    $order->update(['status' => 'confirmed']);

                    // limpiar carrito
                    session()->forget('cart');

                    return response()->json([
                        'status'   => 200,
                        'message'  => 'Orden y documento generados.',
                        'order_id' => $order->id,
                        'doc_id'   => $doc->id,
                        'redirect' => route('store.orders.thankyou', $order->public_uid),
                    ]);
                });
            } catch (\Throwable $e) {
                $lastError = $e;
                //dd($lastError);
                // reintenta
            }
        }

        // Si falla tras 3 intentos
        report($lastError);
        return response()->json([
            'status'  => 500,
            'message' => 'No fue posible emitir el documento. Intenta más tarde.',
        ], 500);
    }

    /** === Usa la misma lógica de totales que tu CartController->makeSummary === */
    private function makeSummaryLikeCart(array $cart): array {
        // Simplificado: puedes llamar directamente a tu CartController->makeSummary si lo mueves a un servicio
        $subtotal = 0; $items = []; $qtyTotal = 0;
        foreach ($cart['items'] as $it) {
            $realQty = $it['real_qty'] ?? $it['qty'];
            $lineNet = (int)($it['line_total'] ?? $it['unit'] * $realQty);
            $subtotal += $lineNet;
            $qtyTotal += (int)$realQty;

            $items[] = [
                'product'  => $it['product'],
                'qty'      => $it['qty_display'] ?? $realQty,
                'unit'     => $it['unit'],
                'line_net' => $lineNet,
                'thumb'    => $it['product']['thumb'] ?? null,
                'options_display' => $this->displayOptions($it['options'] ?? []),
            ];
        }
        $tax   = (int) round($subtotal - ($subtotal/1.19));
        $net   = (int) round($subtotal/1.19);
        $total = $subtotal;

        return [
            'currency' => $cart['currency'] ?? 'CLP',
            'items'    => $items,
            'qty_total'=> $qtyTotal,
            'totals'   => ['subtotal'=>$net,'tax'=>$tax,'total'=>$total],
        ];
    }

    private function displayOptions(array $raw): array {
        // puedes copiar tu buildDisplayOptions del CartController o inyectarlo
        $out = [];
        foreach ($raw as $k=>$v) {
            $vals = is_array($v) ? implode(', ', $v) : (string)$v;
            $out[] = ['group'=>ucfirst(str_replace('_',' ',$k)), 'value'=>$vals];
        }
        return $out;
    }

    private function copyShippingFromAddressId(Order $order, int $addressId): void {
        $addr = \App\Models\Address::with(['country','region','commune'])->findOrFail($addressId);
        OrderAddress::create([
            'order_id'     => $order->id,
            'type'         => 'shipping',
            'line1'        => $addr->line1,
            'line2'        => $addr->line2,
            'reference'    => $addr->reference,
            'country_id'   => $addr->country_id,
            'region_id'    => $addr->region_id,
            'commune_id'   => $addr->commune_id,
            'postal_code'  => $addr->postal_code,
            'latitude'     => $addr->latitude,
            'longitude'    => $addr->longitude,
            'country_name' => optional($addr->country)->name,
            'region_name'  => optional($addr->region)->name,
            'commune_name' => optional($addr->commune)->name,
        ]);
    }

    public function thankyou(string $publicUid)
    {
        $order = \App\Models\Order::where('public_uid', $publicUid)
            ->with(['items','addresses'])
            ->firstOrFail();
        dd($order);
        return view('store.checkout.thankyou', compact('order'));
    }

}

