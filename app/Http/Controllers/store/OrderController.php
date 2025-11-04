<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Cart;
use App\Models\AttributeType;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\Country;
use App\Models\Region;
use App\Models\Commune;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    private const CART_COOKIE = 'cart_uid';

    /** POST /store/checkout/place  (desde "Pagar ahora") */
    public function placeLegacy(Request $request)
    {
        $payload = $request->validate([
            'buyer_name'  => ['nullable','string','max:200'],
            'buyer_email' => ['nullable','email','max:200'],
            'buyer_phone' => ['nullable','string','max:100'],

            'shipping'    => ['nullable','array'],
            'shipping.line1'        => ['nullable','string','max:255'],
            'shipping.line2'        => ['nullable','string','max:255'],
            'shipping.reference'    => ['nullable','string','max:255'],
            'shipping.country_id'   => ['nullable','integer'],
            'shipping.region_id'    => ['nullable','integer'],
            'shipping.commune_id'   => ['nullable','integer'],
            'shipping.country_name' => ['nullable','string','max:120'],
            'shipping.region_name'  => ['nullable','string','max:120'],
            'shipping.commune_name' => ['nullable','string','max:120'],
            'shipping.postal_code'  => ['nullable','string','max:40'],
        ]);

        // 1) Obtener carrito (sesión o BD)
        $cart = session('cart');
        if (empty($cart) || empty($cart['items'])) {
            $cart = $this->hydrateFromDb($request);
        }
        if (empty($cart) || empty($cart['items'])) {
            return response()->json(['status'=>400,'message'=>'Tu carrito está vacío.']);
        }

        // 2) Armar snapshot de envío (requiere dirección)
        $shipping = $this->resolveShippingSnapshot($payload['shipping'] ?? null);
        if (!$shipping) {
            return response()->json([
                'status'=>422,
                'message'=>'Debes agregar una dirección de envío para continuar.'
            ]);
        }

        // 3) Intentar guardar la orden con reintentos
        $attempts = 3;
        $lastEx = null;

        for ($i = 1; $i <= $attempts; $i++) {
            try {
                $result = DB::transaction(function () use ($request, $payload, $cart, $shipping) {

                    // Totales
                    [$itemsCount, $qtyTotal, $subtotalNet, $taxTotal, $grandTotal] = $this->computeTotals($cart);

                    // Crear orden
                    $order = Order::create([
                        'user_id'        => Auth::id(),
                        'cookie_id'      => $request->cookie(self::CART_COOKIE),
                        'buyer_name'     => $payload['buyer_name']  ?? (Auth::user()->name  ?? null),
                        'buyer_email'    => $payload['buyer_email'] ?? (Auth::user()->email ?? null),
                        'buyer_phone'    => $payload['buyer_phone'] ?? null,
                        'currency'       => $cart['currency'] ?? 'CLP',
                        'tax_rate'       => 19.00,
                        'status'         => 'pending_payment',
                        'payment_status' => 'unpaid',
                        'items_count'    => $itemsCount,
                        'qty_total'      => $qtyTotal,
                        'subtotal_net'   => $subtotalNet,
                        'tax_total'      => $taxTotal,
                        'grand_total'    => $grandTotal,
                        'meta_json'      => [
                            'ua'   => request()->userAgent(),
                            'ip'   => request()->ip(),
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

                        OrderItem::create([
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
                    }

                    // Dirección de envío (obligatoria en este flujo)
                    OrderAddress::create(array_merge($shipping, [
                        'order_id' => $order->id,
                        'type'     => 'shipping',
                    ]));

                    // Log de estado
                    $order->logs()->create([
                        'from_status' => null,
                        'to_status'   => 'pending_payment',
                        'message'     => 'Orden creada desde checkout.',
                        'created_by'  => Auth::id(),
                    ]);

                    // Limpieza carrito (en éxito)
                    $this->clearCart($request);

                    return $order;
                }, 5); // 5 = intento de transacción a nivel DB (deadlocks, etc.)

                // ÉXITO
                return response()->json([
                    'status' => 200,
                    'order'  => [
                        'id'         => $result->id,
                        'public_uid' => $result->public_uid,
                        'number'     => $result->id,
                        'total'      => $result->grand_total,
                    ],
                    'redirect' => route('store.orders.thankyou', $result->public_uid),
                ]);
            } catch (\Throwable $e) {
                $lastEx = $e;
                Log::warning("place() intento {$i}/{$attempts} falló: ".$e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                // pequeño backoff opcional (no bloquear request si no quieres):
                // usleep(150000); // 150ms
                continue;
            }
        }

        // Si llegamos aquí, fallaron los 3 intentos
        Log::error('Fallo definitivo al crear orden', ['error'=>$lastEx?->getMessage()]);
        return response()->json([
            'status'  => 500,
            'message' => 'No pudimos crear tu orden en este momento. Intenta nuevamente en unos segundos.',
        ], 500);
    }

    /** GET /store/orders/{uid} */
    public function thankyou(string $publicUid)
    {
        $order = Order::where('public_uid', $publicUid)
            ->with(['items','addresses'])
            ->firstOrFail();

        return view('store.checkout.thankyou', compact('order'));
    }

    // ===================== Helpers =====================

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

    private function getOpenCartModel(Request $request): ?\App\Models\Cart
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

    private function clearCart(Request $request): void
    {
        session()->forget('cart');

        if ($cartModel = $this->getOpenCartModel($request)) {
            $cartModel->items()->delete();
            $cartModel->delete();
        }
        Cookie::queue(Cookie::forget(self::CART_COOKIE));
    }

    /** Calcula totales desde el carrito (precios con IVA incluido) */
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

    /**
     * Prioridad:
     *  1) snapshot enviado en payload (shipping.*)
     *  2) dirección primaria del usuario logueado
     * Devuelve array listo para OrderAddress::create() o null si no hay dirección válida.
     */
    private function resolveShippingSnapshot(?array $shipping): ?array
    {
        // a) Si viene snapshot desde el checkout
        if ($shipping && !empty($shipping['line1'])) {
            $snap = $this->fillGeoNames($shipping);
            return [
                'line1'    => $snap['line1'] ?? '',
                'line2'    => $snap['line2'] ?? null,
                'reference'=> $snap['reference'] ?? null,
                'country_id'   => $snap['country_id'] ?? null,
                'region_id'    => $snap['region_id'] ?? null,
                'commune_id'   => $snap['commune_id'] ?? null,
                'country_name' => $snap['country_name'] ?? null,
                'region_name'  => $snap['region_name'] ?? null,
                'commune_name' => $snap['commune_name'] ?? null,
                'postal_code'  => $snap['postal_code'] ?? null,
                'latitude'     => $snap['latitude'] ?? null,
                'longitude'    => $snap['longitude'] ?? null,
            ];
        }

        // b) Si no vino en payload, usar primaria del usuario
        if (Auth::check()) {
            $addr = Auth::user()->primaryAddress();
            if ($addr) {
                // aseguramos nombres
                $snap = [
                    'line1' => $addr->line1,
                    'line2' => $addr->line2,
                    'reference' => $addr->reference,
                    'country_id' => $addr->country_id,
                    'region_id'  => $addr->region_id,
                    'commune_id' => $addr->commune_id,
                    'country_name' => optional($addr->country)->name,
                    'region_name'  => optional($addr->region)->name,
                    'commune_name' => optional($addr->commune)->name,
                    'postal_code'  => $addr->postal_code,
                    'latitude'     => $addr->latitude,
                    'longitude'    => $addr->longitude,
                ];
                return $snap;
            }
        }

        // c) No hay dirección
        return null;
    }

    /** Si vienen IDs sin nombres, resuelve country/region/commune name */
    private function fillGeoNames(array $snap): array
    {
        // País
        if (!($snap['country_name'] ?? null) && ($snap['country_id'] ?? null)) {
            $c = Country::find($snap['country_id']);
            if ($c) $snap['country_name'] = $c->name;
        }
        // Región
        if (!($snap['region_name'] ?? null) && ($snap['region_id'] ?? null)) {
            $r = Region::find($snap['region_id']);
            if ($r) $snap['region_name'] = $r->name;
        }
        // Comuna
        if (!($snap['commune_name'] ?? null) && ($snap['commune_id'] ?? null)) {
            $cm = Commune::find($snap['commune_id']);
            if ($cm) $snap['commune_name'] = $cm->name;
        }
        return $snap;
    }

    public function repeat(Request $request, $orderId)
    {
        $order = \App\Models\Order::with(['items.product'])
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        // === Traer carrito actual desde sesión (mismo formato del CartController) ===
        $cart = session('cart', ['items' => [], 'currency' => 'CLP']);

        // === Obtener o crear carrito físico en BD ===
        $cartController = app(\App\Http\Controllers\store\CartController::class);
        $cartModel = $cartController->currentCartModel($request);

        // === Atributos que definen combinaciones ===
        $attributes = [
            'size_id', 'paper_id', 'bleed_id', 'finish_id',
            'material_id', 'shape_id', 'print_side_id',
            'mounting_id', 'rolling_id', 'hole_id', 'quantity_id'
        ];

        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) continue;

            // === Extraer opciones y configuración original ===
            //$cfg = json_decode($item->options_json ?? '[]', true) ?: [];
            $options = $item->options_json;
            //$notes   = $cfg['notes'] ?? null;

            // === Buscar precio actualizado ===
            $query = \App\Models\ProductPrice::where('product_id', $product->id);

            foreach ($attributes as $attr) {
                $code = str_replace('_id', '', $attr);
                $val = null;

                if (!empty($options[$code]) && is_array($options[$code])) {
                    $val = $options[$code][0] ?? null;
                }

                // cantidad predefinida
                if ($attr === 'quantity_id' && ($product->uses_quantity ?? false)) {
                    $val = $item->qty ?? null;
                }

                if ($val) $query->where($attr, $val);
                else $query->whereNull($attr);
            }

            $priceRow = $query->first();
            $unit = $priceRow ? (int) $priceRow->price : (int) ($item->unit_price ?? 0);

            // === Determinar cantidad y etiquetas ===
            $qty = (int) $item->qty;
            $realQty = $qty;
            $quantityLabel = null;

            if ($product->uses_quantity ?? false) {
                $val = \App\Models\AttributeValue::find($qty);
                if ($val) {
                    $quantityLabel = $val->name;
                    if (preg_match('/\d+/', $val->name, $m)) {
                        $realQty = (int) $m[0];
                    }
                }
            }

            // === Totales ===
            $lineTotal = ($product->uses_quantity ?? false) ? $unit : $unit * $realQty;

            // === Estructura igual al CartController ===
            $rowId = (string) \Illuminate\Support\Str::uuid();

            $cart['items'][] = [
                'row_id'   => $rowId,
                'product'  => [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'thumb' => optional($product->primaryPhoto())->url ?? asset('img/no-image.jpg'),
                    'uses_quantity' => $product->uses_quantity ?? false,
                ],
                'qty'          => $qty,
                'qty_display'  => $quantityLabel,
                'real_qty'     => $realQty,
                'options'      => $options,
                'notes'        => '',
                'unit'         => $unit,
                'line_total'   => $lineTotal,
                'tax_rate'     => 0.19,
                'attachment'   => null, // No se copian archivos antiguos
            ];

            // === Persistir en BD ===
            $cartModel->items()->create([
                'row_uid'    => $rowId,
                'product_id' => $product->id,
                'qty'        => $qty,
                'config_json'=> json_encode([
                    'options'     => $options,
                    'notes'       => '',
                    'real_qty'    => $realQty,
                    'qty_display' => $quantityLabel,
                    'attachment'  => null,
                ], JSON_UNESCAPED_UNICODE),
                'unit_price' => $unit,
                'line_total' => $lineTotal,
            ]);
        }

        // === Guardar carrito fusionado en sesión ===
        session(['cart' => $cart]);

        return response()->json([
            'status' => 200,
            'message' => 'Los productos de la orden fueron añadidos nuevamente al carrito.',
            'redirect' => route('store.checkout.index'), // si quieres redirigir automáticamente
        ]);
    }


}
