<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileOrdersController extends Controller
{
    /** GET /store/profile/orders  (JSON listado con paginación/filtro) */
    public function index(Request $request)
    {
        $q = Order::query()
            ->where('user_id', Auth::id())
            ->latest('id');

        if ($s = $request->get('status')) {
            $q->where('status', $s);
        }

        $orders = $q->withCount('items')->paginate(12);

        $data = $orders->getCollection()->map(function ($o) {
            return [
                'id'             => $o->id,
                'public_uid'     => $o->public_uid,
                'number'         => $o->id, // o tu formato
                'status'         => $o->status,
                'payment_status' => $o->payment_status,
                'created_at'     => $o->created_at?->format('d-m-Y H:i'),
                'items_count'    => $o->items_count,
                'grand_total'    => (int) $o->grand_total,
                'currency'       => $o->currency ?? 'CLP',
            ];
        })->values();

        return response()->json([
            'status' => 200,
            'data'   => $data,
            'meta'   => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    /** GET /store/profile/orders/{order}  (JSON detalle) */
    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load(['items', 'addresses']);
        $shipping = optional($order->addresses->firstWhere('type','shipping'));

        $items = $order->items->map(function ($it) {
            return [
                'product_name'    => $it->product_name,
                'thumb'           => $it->product_thumb,
                'qty'             => $it->qty_display ?? $it->qty_real ?? $it->qty_raw,
                'unit'            => (int) $it->unit_price_gross,
                'line_total'      => (int) $it->line_total_gross,
                'options_display' => $it->options_display ?? [],
            ];
        })->values();

        return response()->json([
            'status' => 200,
            'order'  => [
                'id'            => $order->id,
                'public_uid'    => $order->public_uid,
                'number'        => $order->id,
                'created_at'    => $order->created_at?->format('d-m-Y H:i'),
                'status'        => $order->status,
                'payment_status'=> $order->payment_status,
                'currency'      => $order->currency ?? 'CLP',
                'subtotal'      => (int) $order->subtotal_net,
                'tax'           => (int) $order->tax_total,
                'total'         => (int) $order->grand_total,
                'items'         => $items,
                'shipping'      => $shipping ? [
                    'line1'        => $shipping->line1,
                    'line2'        => $shipping->line2,
                    'reference'    => $shipping->reference,
                    'country_name' => $shipping->country_name,
                    'region_name'  => $shipping->region_name,
                    'commune_name' => $shipping->commune_name,
                    'postal_code'  => $shipping->postal_code,
                ] : null,
            ],
        ]);
    }
}
