<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemFile;
use App\Models\OrderAddress;
use App\Models\OrderDocument;
use App\Models\AttributeType;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\Country;
use App\Models\Region;
use App\Models\Commune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf; // requiere barryvdh/laravel-dompdf
use App\Services\TransbankService;
use App\Services\WebpayHttpService;


class CheckoutController extends Controller
{
    public function __construct(protected WebpayHttpService $tbk) {}

    public function index() { 
        return view('store.checkout.index'); 
    }

    public function step2() { 
        return view('store.checkout.step2'); 
    }

    public function step3() { 
        return view('store.checkout.step3'); 
    }

    public function guest() { 
        return view('store.checkout.guest'); 
    }

    public function guestSave(Request $request)
    {
        // aquí podrías guardar datos temporales en sesión o tabla guest_orders
        session(['guest_checkout' => $request->all()]);
        return response()->json(['status'=>200,'message'=>'Datos de invitado guardados']);
    }

    public function place(Request $request)
    {
        // 1) Validaciones
        $rules = [
            'doc_type'            => 'required|in:boleta,factura',
            'payment_method'      => 'nullable|string|max:50',
            'notes'               => 'nullable|string|max:1000',
            'shipping_address_id' => 'nullable|integer',

            // snapshot shipping libre (si lo mandas en vez de id)
            'shipping_line1'      => 'nullable|string|max:255',
            'shipping_line2'      => 'nullable|string|max:255',
            'shipping_reference'  => 'nullable|string|max:255',
            'shipping_country_id' => 'nullable|integer',
            'shipping_region_id'  => 'nullable|integer',
            'shipping_commune_id' => 'nullable|integer',
        ];
        if ($request->doc_type === 'factura') {
            $rules = array_merge($rules, [
                'receiver_rut'        => 'required|string|max:20',
                'receiver_name'       => 'required|string|max:255',
                'receiver_giro'       => 'required|string|max:255',
                'receiver_address'    => 'required|string|max:255',
                'receiver_country_id' => 'required|integer',
                'receiver_region_id'  => 'required|integer',
                'receiver_commune_id' => 'required|integer',
            ]);
        }
        $data = $request->validate($rules);

        // 2) Carrito desde sesión (o BD si vacío)
        $cart = session('cart');
        if (empty($cart) || empty($cart['items'])) {
            // si ya tienes un helper en tu CartController úsalo,
            // o reusa el hydrateFromDb() del OrderController
            $cart = $this->hydrateFromDb($request);
        }
        if (empty($cart) || empty($cart['items'])) {
            return response()->json(['status'=>400,'message'=>'Carrito vacío.'], 400);
        }

        //dd($cart);

        // 3) Dirección de envío
        $shippingSnap = null;
        if (!empty($data['shipping_address_id'])) {
            // copia desde Address a OrderAddress
            $addr = Address::with(['country','region','commune'])
                    ->where('addressable_id', Auth::id())
                    ->where('addressable_type', 'App\\Models\\User')                    
                    ->findOrFail((int)$data['shipping_address_id']);
            $shippingSnap = [
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
            ];
        } elseif (!empty($data['shipping_line1'])) {
            $shippingSnap = [
                'line1'        => $data['shipping_line1'],
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
            ];
        } else {
            // fallback: primaria del usuario
            $addr = Auth::user()?->primaryAddress();
            if ($addr) {
                $shippingSnap = [
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
                ];
            }
        }
        if (!$shippingSnap) {
            return response()->json(['status'=>422,'message'=>'Debes registrar una dirección de envío.'], 422);
        }

        // 4) Intentos transaccionales
        $attempts = 0; $lastEx = null;
        while ($attempts < 3) {
            $attempts++;
            try {
                $result = \DB::transaction(function () use ($cart, $data, $shippingSnap, $request) {
                    // Totales
                    [$itemsCount, $qtyTotal, $subtotalNet, $taxTotal, $grandTotal] = $this->computeTotals($cart);

                    // Orden (queda pendiente de pago)
                    $order = Order::create([
                        'user_id'        => Auth::id(),
                        'cookie_id'      => $request->cookie('cart_uid'),
                        'buyer_name'     => Auth::user()->name ?? null,
                        'buyer_email'    => Auth::user()->email ?? null,
                        'buyer_phone'    => optional(Auth::user()->primaryPhone())->number,
                        'currency'       => $cart['currency'] ?? 'CLP',
                        'tax_rate'       => 19.00,
                        'status'         => 'pending_payment',
                        'payment_status' => 'unpaid',
                        'items_count'    => $itemsCount,
                        'qty_total'      => $qtyTotal,
                        'subtotal_net'   => $subtotalNet,
                        'tax_total'      => $taxTotal,
                        'grand_total'    => $grandTotal,
                        'notes'          => $data['notes'] ?? null,
                        'doc_type'       => $data['doc_type'],
                        'public_uid'     => \Str::uuid(), // asegúrate que exista esta col; ya la usas en thankyou
                        'meta_json'      => [
                            'payment_method' => $data['payment_method'] ?? 'webpay',
                            'ua' => request()->userAgent(), 'ip' => request()->ip(),
                            'from' => 'checkout',
                        ],
                    ]);

                    // Items
                    foreach ($cart['items'] as $it) {
                        $qtyReal   = (int) ($it['real_qty'] ?? $it['qty'] ?? 1);
                        $lineGross = (int) ($it['line_total'] ?? 0);
                        $unitGross = (int) ($it['unit'] ?? 0);

                        $lineNet = (int) round($lineGross / 1.19);
                        $lineTax = $lineGross - $lineNet;
                        $unitNet = (int) round($unitGross / 1.19);
                        $unitTax = $unitGross - $unitNet;

                        $item = OrderItem::create([
                            'order_id'          => $order->id,
                            'product_id'        => $it['product']['id'] ?? null,
                            'product_name'      => $it['product']['name'] ?? 'Producto',
                            'product_thumb'     => $it['product']['thumb'] ?? null,
                            'uses_quantity'     => (bool)($it['product']['uses_quantity'] ?? false),
                            'qty_raw'           => (int)($it['qty'] ?? 1),
                            'qty_display'       => $it['qty_display'] ?? null,
                            'qty_real'          => $qtyReal,
                            'unit_price_gross'  => $unitGross,
                            'unit_price_net'    => $unitNet,
                            'tax_amount_unit'   => $unitTax,
                            'line_total_gross'  => $lineGross,
                            'line_total_net'    => $lineNet,
                            'line_tax_total'    => $lineTax,
                            'options_json'      => $it['options'] ?? null,
                            'options_display'   => $this->buildDisplayOptions($it['options'] ?? []),
                            'options_map'       => $it['options_map'] ?? null,
                        ]);

                        // --- mover adjuntos (igual que tu código actual) ---
                        if (!empty($it['attachment']) && is_array($it['attachment'])) {
                            $att = $it['attachment'];
                            $tmpPath = $att['path'] ?? null;
                            $tmpDisk = $att['disk'] ?? 'public_uploads';
                            if ($tmpPath && \Storage::disk($tmpDisk)->exists($tmpPath)) {
                                $destDir = 'documents/orders/' . $order->id . '/items/' . $item->id;
                                \Storage::disk('public_uploads')->makeDirectory($destDir);
                                $filename = basename($tmpPath);
                                $newPath  = $destDir . '/' . $filename;
                                \Storage::disk($tmpDisk)->move($tmpPath, $newPath);
                                OrderItemFile::create([
                                    'order_item_id' => $item->id,
                                    'path'          => $newPath,
                                    'original_name' => $att['name'] ?? $filename,
                                    'mime'          => $att['mime'] ?? null,
                                    'size'          => $att['size'] ?? null,
                                ]);
                            }
                        }
                    }

                    // Dirección
                    OrderAddress::create(array_merge($shippingSnap, [
                        'order_id' => $order->id,
                        'type'     => 'shipping',
                    ]));

                    // Documento tributario → queda PENDING sin PDF
                    OrderDocument::create([
                        'order_id' => $order->id,
                        'type'     => $data['doc_type'], // boleta|factura
                        'status'   => 'pending',
                        'receiver_rut'        => $data['receiver_rut']        ?? null,
                        'receiver_name'       => $data['receiver_name']       ?? null,
                        'receiver_giro'       => $data['receiver_giro']       ?? null,
                        'receiver_address'    => $data['receiver_address']    ?? null,
                        'receiver_country_id' => $data['receiver_country_id'] ?? null,
                        'receiver_region_id'  => $data['receiver_region_id']  ?? null,
                        'receiver_commune_id' => $data['receiver_commune_id'] ?? null,
                        'subtotal_net' => $subtotalNet,
                        'tax_total'    => $taxTotal,
                        'grand_total'  => $grandTotal,
                        'currency'     => $order->currency,
                    ]);

                    // Log
                    $order->logs()->create([
                        'from_status' => null,
                        'to_status'   => 'pending_payment',
                        'message'     => 'Orden creada desde checkout (pendiente de pago Webpay).',
                        'created_by'  => Auth::id(),
                    ]);

                    return $order;
                }, 5);

                // === Crear transacción Webpay para $result->grand_total ===
                $amount    = (int) $result->grand_total;
                $returnUrl = config('services.transbank.return_url');
                
                $tbkData = $this->tbk->create($amount, $returnUrl, (string)Auth::id());

                if (empty($tbkData['token']) || empty($tbkData['url'])) {
                    \Log::error('Webpay create sin token/url', ['resp' => $tbkData]);
                    return response()->json(['status'=>500,'message'=>'No fue posible iniciar el pago.'], 500);
                }

                
                session([
                    'tbk' => [
                        'token'     => $tbkData['token'],
                        'buy_order'  => $tbkData['buy_order'],
                        'amount'    => $tbkData['amount'],
                        'order_id'  => $result->id,
                        'public_uid'=> $result->public_uid,
                    ]
                ]);

                $result->update([
                    'meta_json' => array_merge($result->meta_json ?? [], [
                        'tbk' => [
                            'token'    => $tbkData['token'],
                            'buy_order' => $tbkData['buy_order'],
                            'amount'   => $tbkData['amount'],
                        ],
                    ])
                ]);

                return response()->json([
                    'status'   => 200,
                    'redirect' => $tbkData['url'].'?token_ws='.$tbkData['token'],
                ]);


                // (opcional) guarda en meta_json de la orden
                $result->update([
                    'meta_json' => array_merge($result->meta_json ?? [], [
                        'tbk' => [
                            'token'    => $tbkData['token'],
                            'buy_order' => $tbkData['buy_order'],
                            'amount'   => $tbkData['amount'],
                        ],
                    ])
                ]);

                return response()->json([
                    'status'   => 200,
                    'redirect' => $tbkData['url'].'?token_ws='.$tbkData['token'], // <<-- TU FRONT ya espera "redirect"
                ]);

            } catch (\Throwable $e) {
                $lastEx = $e;
                \Log::warning("CheckoutController@place intento {$attempts}/3: ".$e->getMessage());
            }
        }

        \Log::error('CheckoutController@place fallo definitivo', ['error'=>$lastEx?->getMessage()]);
        return response()->json(['status'=>500,'message'=>'No fue posible emitir el documento.'], 500);
    }

    public function thankyou(string $publicUid)
    {
        $order = Order::where('public_uid', $publicUid)
            ->with(['items','addresses','documents'])
            ->firstOrFail();

        $doc = $order->documents->sortByDesc('id')->first();
        $pdfUrl = ($doc && $doc->pdf_path)
            ? \Storage::disk('public_uploads')->url($doc->pdf_path)
            : null;

        return view('store.checkout.thankyou', compact('order','doc','pdfUrl'));
    }

    private function computeTotals(array $cart): array
    {
        $itemsCount = count($cart['items']);
        $qtyTotal   = 0;
        $subtotalNet= 0;
        $taxTotal   = 0;
        $grandTotal = 0;

        foreach ($cart['items'] as $it) {
            $qtyReal   = (int) ($it['real_qty'] ?? $it['qty'] ?? 1);
            $lineGross = (int) ($it['line_total'] ?? 0);
            $lineNet   = (int) round($lineGross / 1.19);
            $lineTax   = $lineGross - $lineNet;

            $qtyTotal   += $qtyReal;
            $subtotalNet+= $lineNet;
            $taxTotal   += $lineTax;
            $grandTotal += $lineGross;
        }
        return [$itemsCount, $qtyTotal, $subtotalNet, $taxTotal, $grandTotal];
    }

    /** Construye nombres bonitos para opciones */
    private function buildDisplayOptions(array $raw): array
    {
        if (empty($raw)) return [];

        $codes = array_keys($raw);
        $types = AttributeType::whereIn('code', $codes)->get()->keyBy('code');

        $valueIds = collect($raw)
            ->values()->flatten()
            ->filter(fn($v)=>is_scalar($v) && is_numeric($v))
            ->map(fn($v)=>(int)$v)->unique()->values();

        $values = $valueIds->isNotEmpty()
            ? AttributeValue::whereIn('id', $valueIds)->get()->keyBy('id')
            : collect();

        $out = [];
        foreach ($raw as $code => $ids) {
            if ($code === 'quantity') {
                $vals = collect($ids)->map(function($v) use ($values) {
                    $vv = $values->get((int)$v);
                    return $vv ? $vv->name : (string)$v;
                })->filter()->values()->all();
                if ($vals) $out[] = ['group'=>'Cantidad','value'=>implode(', ', $vals)];
                continue;
            }
            $type  = $types->get($code);
            $label = $type ? $type->name : ucfirst(str_replace('_',' ',$code));
            $vals  = collect($ids)->map(fn($id)=>optional($values->get((int)$id))->name)->filter()->values()->all();
            if ($vals) $out[] = ['group'=>$label,'value'=>implode(', ', $vals)];
        }
        return $out;
    }

    private function getOpenCartModel(Request $request): ?Cart
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->where('status','open')->first();
        }
        $cookieId = $request->cookie(self::CART_COOKIE);
        if ($cookieId) {
            return Cart::where('cookie_id', $cookieId)->where('status','open')->first();
        }
        return null;
    }

    /** Reconstituye carrito desde BD si la sesión está vacía */
    private function hydrateFromDb(Request $request): ?array
    {
        $cartModel = $this->getOpenCartModel($request);
        if (!$cartModel) return null;

        $items = [];
        foreach ($cartModel->items()->with('product')->get() as $row) {
            $cfg = json_decode($row->config_json ?? '[]', true) ?: [];
            $items[] = [
                'row_id' => $row->row_uid ?? (string) Str::uuid(),
                'product'=> [
                    'id'    => $row->product_id,
                    'name'  => $row->product?->name ?? 'Producto',
                    'thumb' => optional($row->product?->primaryPhoto())->url ?? asset('img/no-image.jpg'),
                    'uses_quantity' => (bool)($row->product?->uses_quantity ?? false),
                ],
                'qty'          => (int) $row->qty,
                'qty_display'  => $cfg['qty_display'] ?? null,
                'real_qty'     => $cfg['real_qty'] ?? (int) $row->qty,
                'options'      => $cfg['options'] ?? [],
                'unit'         => (int) $row->unit_price,
                'line_total'   => (int) ($row->line_total ?? ($row->unit_price * max(1,(int)$row->qty))),
            ];
        }
        $cart = ['items'=>$items,'currency'=>'CLP'];
        session(['cart'=>$cart]);

        return $cart;
    }

    private function issueTaxDocument(Order $order): ?OrderDocument
    {
        $doc = $order->documents()->orderByDesc('id')->first();

        if (!$doc) return null;

        // Recalcular totales por si acaso
        $subtotalNet = (int) $order->subtotal_net;
        $taxTotal    = (int) $order->tax_total;
        $grandTotal  = (int) $order->grand_total;

        $doc->update([
            'subtotal_net' => $subtotalNet,
            'tax_total'    => $taxTotal,
            'grand_total'  => $grandTotal,
            'currency'     => $order->currency,
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.documents.tax', [
            'order' => $order->fresh(['items']),
            'doc'   => $doc,
        ]);

        $disk = 'public_uploads';
        $dir  = 'documents/orders/'.$order->id;
        $filename = 'document_'.$doc->type.'_'.$doc->id.'.pdf';
        $path = $dir.'/'.$filename;

        \Storage::disk($disk)->makeDirectory($dir);
        \Storage::disk($disk)->put($path, $pdf->output());

        $doc->update([
            'pdf_path'  => $path,
            'status'    => 'issued',
            'issued_at' => now(),
        ]);

        return $doc;
    }

    public function payReturn(Request $request)
    {
        $token = $request->input('token_ws');
        if (!$token) {
            return view('store.payment-result', ['ok'=>false,'message'=>'Token no recibido.','details'=>null]);
        }

        $res = $this->tbk->commit($token); // <-- array

        //\Log::warning("Respuesta: " . json_encode($res));

        $saved      = session('tbk', []);
        $amountOk   = ((int)($res['amount'] ?? 0)) === ((int)($saved['amount'] ?? -1));
        $orderOk    = ($res['buy_order'] ?? '') === ($saved['buy_order'] ?? '');
        $authorized = ($res['status'] ?? '') === 'AUTHORIZED';

        //\Log::warning("Saved: " . json_encode($saved));

        $order = !empty($saved['order_id'])
            ? Order::with(['documents','items'])->find($saved['order_id'])
            : null;

        if ($authorized && $amountOk && $orderOk && $order) {
            $order->update([
                'status'         => 'paid',
                'payment_status' => 'paid',
                'paid_at'        => now(),
                'meta_json'      => array_merge($order->meta_json ?? [], [
                    'tbk_commit' => $res,
                ]),
            ]);
            $order->logs()->create([
                'from_status' => 'pending_payment',
                'to_status'   => 'paid',
                'message'     => 'Pago Webpay autorizado.',
                'created_by'  => Auth::id(),
            ]);

            $this->issueTaxDocument($order);

            session()->forget('cart');
            if ($cartModel = $this->getOpenCartModel($request)) {
                $cartModel->items()->delete(); $cartModel->delete();
            }
            \Cookie::queue(\Cookie::forget('cart_uid'));

            return redirect()->route('store.orders.thankyou', $order->public_uid);
        }

        if ($order) {
            $order->update([
                'status'         => 'payment_failed',
                'payment_status' => 'unpaid',
                'meta_json'      => array_merge($order->meta_json ?? [], ['tbk_error' => $res]),
            ]);
            $order->logs()->create([
                'from_status' => 'pending_payment',
                'to_status'   => 'payment_failed',
                'message'     => 'Pago Webpay rechazado o inválido.',
                'created_by'  => Auth::id(),
            ]);
        }

        //dd($res);
        return view('store.checkout.payment-result', [
            'ok'      => false,
            'message' => 'Pago rechazado o inválido.',
            'details' => (object) $res, // por compatibilidad con tu blade si lo usas
        ]);

    }


}

