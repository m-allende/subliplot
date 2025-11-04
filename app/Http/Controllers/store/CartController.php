<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\AttributeType;
use App\Models\AttributeValue;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductPrice;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File as FileRule;


class CartController extends Controller
{
    private const CART_COOKIE = 'cart_uid';
    private const COOKIE_DAYS = 365;

    // -------- Sesión ----------
    protected function getCart(): array {
        return session('cart', ['items'=>[], 'currency'=>'CLP']);
    }
    protected function putCart(array $cart): void {
        session(['cart'=>$cart]);
    }
    protected function resetSessionCart(): void {
        session()->forget('cart');
    }

    // -------- BD / Cookie helpers ----------
    protected function currentCookieId(Request $request): ?string {
        return $request->cookie(self::CART_COOKIE);
    }

    protected function ensureCookieId(Request $request): string {
        $cid = $this->currentCookieId($request);
        if (!$cid) {
            $cid = (string) Str::uuid();
            Cookie::queue(Cookie::make(self::CART_COOKIE, $cid, 60 * 24 * self::COOKIE_DAYS));
        }
        return $cid;
    }

    /** Devuelve el Cart "open" del usuario logueado o del invitado (cookie). Lo crea si no existe. */
    protected function getOrCreateCartModel(Request $request): Cart
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id(), 'status' => 'open'],
                ['cookie_id' => null]
            );
        } else {
            $cid  = $this->ensureCookieId($request);
            $cart = Cart::firstOrCreate(
                ['cookie_id' => $cid, 'status' => 'open'],
                ['user_id' => null]
            );
        }
        return $cart;
    }

    /** Carga el carrito desde BD a sesión si la sesión está vacía */
    protected function hydrateSessionFromDb(Request $request): void
    {
        $cartSess = $this->getCart();
        //dd($cartSess);
        if (!empty($cartSess['items'])) return;

        $model = $this->getOrCreateCartModel($request);
        $items = [];
        foreach ($model->items()->with('product')->get() as $row) {
            $cfg = json_decode($row->config_json ?? '[]', true) ?: [];

            $items[] = [
                'row_id'   => $row->row_uid ?? (string) Str::uuid(),
                'product'  => [
                    'id'    => $row->product_id,
                    'name'  => $row->product?->name ?? 'Producto',
                    'thumb' => optional($row->product?->primaryPhoto())->url ?? asset('img/no-image.jpg'),
                    'uses_quantity' => (bool)($row->product?->uses_quantity ?? false),
                ],
                'qty'          => (int)$row->qty,
                'real_qty'     => $cfg['real_qty'] ?? (int)$row->qty,
                'qty_display'  => $cfg['qty_display'] ?? null,
                'options'      => $cfg['options'] ?? [],
                'notes'        => $cfg['notes'] ?? null,
                'unit'         => (int)$row->unit_price,
                'line_total'   => (int)($row->line_total ?? $row->unit_price * max(1,(int)$row->qty)),
                'tax_rate'     => 0.19,
                'attachment'   => $cfg['attachment'] ?? null, // <-- importante
            ];
        }
        $this->putCart(['items'=>$items, 'currency'=>'CLP']);
    }

    // --------- Acciones ----------
    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'qty'        => ['required','integer','min:1'],
            'options'    => ['array'],
            'notes'      => ['nullable','string','max:1000'],
            'attachment' => ['nullable', FileRule::types(['jpg','jpeg','png','webp','gif','pdf'])->max(20 * 1024)],
        ]);

        $product = Product::findOrFail($data['product_id']);

        $usesQuantity = $product->uses_quantity ?? false;
        $qty = (int) $data['qty'];
        $realQty = $qty;  // cantidad “numérica real” para calcular totales

        $quantityLabel = null;

        if ($usesQuantity) {
            $value = AttributeValue::find($qty);
            if ($value) {
                // Guardamos su nombre y, opcionalmente, un número real si lo contiene
                $quantityLabel = $value->name; // “100 unidades” o “por 100 tarjetas”
                // Extrae número si el name es “100 unidades” (opcional)
                if (preg_match('/\d+/', $value->name, $m)) {
                    $realQty = (int) $m[0];
                }
            }
        }

        // Precio unitario (de momento demo)
        //$unit = 1000; // CLP neto

        $attributes = [
            'size_id', 'paper_id', 'bleed_id', 'finish_id',
            'material_id', 'shape_id', 'print_side_id',
            'mounting_id', 'rolling_id', 'hole_id', 'quantity_id'
        ];

        // Comenzamos la búsqueda de precio
        $query = ProductPrice::where('product_id', $product->id);

        // Mapeamos los valores enviados en "options"
        foreach ($attributes as $attr) {
            $code = str_replace('_id', '', $attr);
            $value = null;

            if (!empty($data['options'][$code]) && is_array($data['options'][$code])) {
                $value = $data['options'][$code][0] ?? null;
            }

            if ($attr === 'quantity_id' && ($product->uses_quantity ?? false)) {
                $value = $data['qty'] ?? null;
            }

            if ($value) {
                $query->where($attr, $value);
            } else {
                $query->whereNull($attr);
            }
        }

        // Buscar coincidencia exacta
        $priceRow = $query->first();
        $unit = $priceRow ? (int) $priceRow->price : 0;

        if ($unit <= 0) {
            return response()->json([
                'status' => 400,
                'message' => 'No existe un precio válido para esta combinación de opciones.',
            ]);
        }

        $rowId = (string) Str::uuid();

        // === si viene archivo, guárdalo en ruta TEMPORAL del carrito ===
        $attachmentMeta = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            $bucket = Auth::check()
                ? 'user_'.Auth::id()
                : 'guest_'.$this->ensureCookieId($request);

            // carpeta temporal por ítem
            $dir  = "temp/cart/{$bucket}/{$rowId}";
            $path = $file->store($dir, 'public_uploads');

            $attachmentMeta = [
                'disk' => 'public_uploads',
                'dir'  => $dir,
                'path' => $path,
                'url'  => Storage::disk('public_uploads')->url($path),
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'is_temp' => true,
            ];
        }


        $qty  = (int) $data['qty'];

        $usesQuantity = $product->uses_quantity ?? false;
        $realQty = (int) ($data['qty'] ?? 1);

        if ($usesQuantity) {
            $lineTotal = $unit; // ya incluye el pack
        } else {
            $lineTotal = $unit * $realQty;
        }


        // ---- SESIÓN
        $cart = $this->getCart();
        $cart['items'][] = [
            'row_id'   => $rowId,
            'product'  => [
                'id'    => $product->id,
                'name'  => $product->name,
                'thumb' => optional($product->primaryPhoto())->url ?? asset('img/no-image.jpg'),
                'uses_quantity' => $usesQuantity,
            ],
            'qty'          => $qty,             // ID o número según caso
            'qty_display'  => $quantityLabel,   // Nombre legible (solo si aplica)
            'real_qty'     => $realQty,         // Valor numérico para cálculos
            'options'  => $data['options'] ?? [],
            'notes'    => $data['notes'] ?? null,
            'unit' => $unit,
            'line_total' => $lineTotal,
            'tax_rate' => 0.19,
            'attachment' => $attachmentMeta,  // <-- NUEVO
        ];
        $this->putCart($cart);

        // ---- BD (si hay usuario logueado o invitado con cookie)
        $cartModel = $this->getOrCreateCartModel($request);

        $cartModel->items()->create([
            'row_uid'    => $rowId,
            'product_id' => $product->id,
            'qty'        => $qty,
            'config_json'=> json_encode([
                'options'     => $data['options'] ?? [],
                'notes'       => $data['notes'] ?? null,
                'real_qty'    => $realQty,
                'qty_display' => $quantityLabel,
                'attachment'  => $attachmentMeta,   // <-- AQUÍ SÍ
            ], JSON_UNESCAPED_UNICODE),
            'unit_price' => $unit,
            'line_total' => $lineTotal,
        ]);


        return response()->json([
            'status'  => 200,
            'summary' => $this->makeSummary($this->getCart()),
        ]);
    }

    public function update(Request $request, string $rowId)
    {
        $data = $request->validate([
            'qty'     => ['nullable','integer','min:1'],
            'options' => ['nullable','array'],   // por si luego actualizas opciones desde checkout
            'notes'   => ['nullable','string','max:1000'],
        ]);

        // 1) Cargar carrito de sesión
        $cart = $this->getCart();
        $items = collect($cart['items'] ?? []);
        $idx = $items->search(fn($it) => ($it['row_id'] ?? '') === $rowId);
        if ($idx === false) {
            // si la sesión vino vacía, intenta hidratar desde BD y busca de nuevo
            $this->hydrateSessionFromDb($request);
            $cart = $this->getCart();
            $items = collect($cart['items'] ?? []);
            $idx = $items->search(fn($it) => ($it['row_id'] ?? '') === $rowId);
            if ($idx === false) {
                return response()->json(['status'=>404,'message'=>'Ítem no encontrado.']);
            }
        }

        $item = $items[$idx];
        $product = Product::find($item['product']['id']);
        if (! $product) {
            return response()->json(['status'=>404,'message'=>'Producto no existe.']);
        }

        // 2) Aplicar cambios (qty / options / notes)
        if (array_key_exists('qty', $data) && $data['qty'] !== null) {
            $item['qty'] = (int)$data['qty'];
            $item['real_qty'] = (int)$data['qty'];
        }
        if (!empty($data['options'])) {
            $item['options'] = $data['options']; // ej: { size:[1], paper:[6], bleed:[11] }
        }
        if (array_key_exists('notes',$data)) {
            $item['notes'] = $data['notes'];
        }

        // 3) Recalcular precio usando la misma lógica de "add"
        $attributes = [
            'size_id','paper_id','bleed_id','finish_id',
            'material_id','shape_id','print_side_id',
            'mounting_id','rolling_id','hole_id','quantity_id'
        ];

        $query = ProductPrice::where('product_id', $product->id);

        foreach ($attributes as $attr) {
            $code = str_replace('_id','',$attr);
            $val = null;

            // valor desde opciones del item (mantén el 1er valor si es multi)
            if (!empty($item['options'][$code])) {
                $arr = is_array($item['options'][$code]) ? $item['options'][$code] : [$item['options'][$code]];
                $val = $arr[0] ?? null;
            }

            // quantity_id viene desde qty cuando el producto usa cantidades predefinidas
            if ($attr === 'quantity_id' && ($product->uses_quantity ?? false)) {
                $val = $item['qty'] ?? null;

                // además, refrescamos el label de cantidad (qty_display) si existe un nombre
                $qVal = AttributeValue::find((int)$val);
                if ($qVal) {
                    $item['qty_display'] = $qVal->name;
                    // opcional: real_qty numérico si el name tiene número (100, 200…)
                    if (preg_match('/\d+/', $qVal->name, $m)) {
                        $item['real_qty'] = (int)$m[0];
                    }
                }
            }

            if ($val !== null && $val !== '') {
                $query->where($attr, $val);
            } else {
                $query->whereNull($attr);
            }
        }

        $priceRow = $query->first();
        $unit = $priceRow ? (int)$priceRow->price : 0;

        if ($unit <= 0) {
            return response()->json([
                'status'=>400,
                'message'=>'No existe un precio válido para esta combinación.',
            ]);
        }

        // 4) Recalcular totales de la línea
        if ($product->uses_quantity ?? false) {
            // pack cerrado: el precio ya es por la cantidad elegida
            $item['line_total'] = $unit;
        } else {
            // cantidad libre: multiplicar por real_qty
            $item['line_total'] = $unit * max(1, (int)($item['real_qty'] ?? $item['qty'] ?? 1));
        }
        $item['unit'] = $unit;

        // 5) Guardar en sesión
        $items[$idx] = $item;
        $cart['items'] = $items->values()->all();
        $this->putCart($cart);

        // 6) Persistir en BD
        $cartModel = $this->getOrCreateCartModel($request);
        $cartModel->items()->where('row_uid',$rowId)->update([
            'qty'        => (int)$item['qty'],
            'config_json'=> json_encode([
                'options'     => $item['options'] ?? [],
                'notes'       => $item['notes'] ?? null,
                'real_qty'    => $item['real_qty'] ?? $item['qty'],
                'qty_display' => $item['qty_display'] ?? null,
            ], JSON_UNESCAPED_UNICODE),
            'unit_price' => (int)$item['unit'],
            'line_total' => (int)$item['line_total'],
        ]);

        return response()->json([
            'status'=>200,
            'message'=>'Ítem actualizado.',
            'summary'=>$this->makeSummary($this->getCart()),
        ]);
    }


    public function remove(Request $request, string $rowId)
    {
        // --- 1) Carrito actual ---
        $cart = $this->getCart();

        // Busca el ítem en sesión (ANTES de quitarlo)
        $itemToDelete = collect($cart['items'] ?? [])->first(fn($it) => ($it['row_id'] ?? '') === $rowId);

        // 1.1) Borrar adjunto físico temporal si existía
        if (!empty($itemToDelete['attachment']['disk']) && !empty($itemToDelete['attachment']['dir'])) {
            try {
                Storage::disk($itemToDelete['attachment']['disk'])->deleteDirectory($itemToDelete['attachment']['dir']);
            } catch (\Throwable $e) { /* ignore */ }
        }

        // --- 2) Quitar el ítem de la sesión ---
        $items = collect($cart['items'] ?? [])
            ->reject(fn($it) => ($it['row_id'] ?? '') === $rowId)
            ->values()
            ->all();

        $cart['items'] = $items;

        // --- 3) Guardar sesión actualizada ---
        session(['cart' => $cart]);
        session()->save();

        // --- 4) Eliminar ítem de la BD (y borrar adjunto si estaba sólo en BD) ---
        $cartModel = $this->getOrCreateCartModel($request);
        $row = $cartModel->items()->where('row_uid', $rowId)->first();

        if ($row) {
            $cfg = json_decode($row->config_json ?? '[]', true) ?: [];
            if (!empty($cfg['attachment']['disk']) && !empty($cfg['attachment']['dir'])) {
                try {
                    Storage::disk($cfg['attachment']['disk'])->deleteDirectory($cfg['attachment']['dir']);
                } catch (\Throwable $e) { /* ignore */ }
            }
            $row->delete();
        }

        // --- 5) Si el carrito queda vacío, limpiar todo ---
        if (empty($cart['items'])) {
            $cartModel->items()->delete();
            $cartModel->delete();
            session()->forget('cart');
            session()->save();
            Cookie::queue(Cookie::forget(self::CART_COOKIE));
        }

        // --- 6) Resumen ---
        return response()->json([
            'status'  => 200,
            'deleted' => true,
            'summary' => $this->makeSummary($this->getCart()),
        ]);
    }

    public function clear(Request $request)
    {
        // 🔹 Borrar adjuntos temporales de todos los ítems que estén en sesión
        $cart = $this->getCart();
        foreach (($cart['items'] ?? []) as $it) {
            if (!empty($it['attachment']['disk']) && !empty($it['attachment']['dir'])) {
                try {
                    Storage::disk($it['attachment']['disk'])->deleteDirectory($it['attachment']['dir']);
                } catch (\Throwable $e) { /* ignore */ }
            }
        }

        // Sesión
        $this->resetSessionCart();

        // BD
        $cartModel = $this->getOrCreateCartModel($request);
        $cartModel->items()->delete(); // dejamos el cart "open" vacío

        return response()->json([
            'status'=>200,
            'summary'=>$this->makeSummary($this->getCart()),
        ]);
    }


    public function summary(Request $request)
    {
        // Si la sesión está vacía pero en BD existe carrito abierto, hidratar
        $this->hydrateSessionFromDb($request);

        $cart = $this->getCart();
        return response()->json([
            'status'=>200,
            'summary'=>$this->makeSummary($cart),
        ]);
    }

    // --------- Summary (igual al tuyo; sin cambios de lógica) ----------
    protected function makeSummary(array $cart): array
    {
        $items = [];
        $subtotal = 0;
        $qtyTotal = 0;

        foreach ($cart['items'] as $it) {
            $realQty = $it['real_qty'] ?? $it['qty'];
            $lineNet = $it['line_total'];
            $subtotal += $lineNet;
            $qtyTotal += (int)$realQty;

            $detailQty = $it['qty_display'] ?? $realQty;

            $optText = [];
            $optionsMap = [];

            foreach (($it['options'] ?? []) as $code => $ids) {
                $ids = is_array($ids) ? $ids : [$ids];
                $ids = collect($ids)
                    ->flatten()
                    ->filter(fn($v) => is_scalar($v) || is_numeric($v))
                    ->values()
                    ->all();

                $label = ucfirst(str_replace('_', ' ', $code));
                $values = implode(', ', $ids);
                $optText[] = "{$label}: {$values}";

                // Mapa code_id => id (para el front). Si es multi, tomamos el primero.
                if (!empty($ids)) {
                    $optionsMap[$code . '_id'] = $ids[0];
                }
            }

            // Si el producto usa cantidades “predefinidas”, también mapear quantity_id
            if (!empty($it['product']['uses_quantity']) && is_numeric($it['qty'])) {
                $optionsMap['quantity_id'] = (int)$it['qty'];
            }

            $items[] = [
                'row_id'          => $it['row_id'],
                'product'         => $it['product'],
                'qty'             => $detailQty, // valor legible (lo sigues mostrando igual)
                'qty_raw'         => $it['qty'], // <-- ID crudo que seleccionó el usuario
                'unit'            => $it['unit'],
                'line_net'        => $lineNet,
                'thumb'           => $it['product']['thumb'] ?? asset('img/no-image.jpg'),
                'options'         => $optText ? implode(' · ', $optText) : null,
                'options_display' => $this->buildDisplayOptions($it['options']),
                'options_map'     => $optionsMap, // <-- NUEVO para el checkout
                'attachment'      => $it['attachment'] ?? null, // si lo quieres en el front
            ];
        }


        $tax = (int) round($subtotal - ($subtotal / 1.19));
        $net = (int) round($subtotal / 1.19);
        $total = $subtotal;

        return [
            'currency'     => $cart['currency'] ?? 'CLP',
            'items'        => $items,
            'items_count'  => count($cart['items']),
            'qty_total'    => $qtyTotal,
            'totals'       => [
                'subtotal' => $net,
                'tax'      => $tax,
                'total'    => $total,
            ],
        ];
    }


    // --------- Nombres bonitos (lo dejaste bien; lo mantengo) ----------
    private function buildDisplayOptions(array $raw): array
    {
        if (empty($raw)) return [];

        $codes = array_keys($raw);
        $types = AttributeType::whereIn('code', $codes)->get()->keyBy('code');

        $valueIds = collect($raw)
            ->values()->flatten()
            ->filter(fn($v)=>is_scalar($v) && is_numeric($v))
            ->map(fn($v)=>(int)$v)->unique()->values();

        $values   = $valueIds->isNotEmpty()
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

    public function currentCartModel(Request $request): \App\Models\Cart
    {
        return $this->getOrCreateCartModel($request);
    }
}
