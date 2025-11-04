<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class CartService {
  const COOKIE_KEY = 'cart_cookie_id';
  const COOKIE_TTL_MIN = 60*24*30; // 30 días

  public function current(): Cart {
    // si hay usuario => 1 carrito "open" por user
    if (Auth::check()) {
      $cart = Cart::firstOrCreate(
        ['user_id'=>Auth::id(), 'status'=>'open'],
        ['cookie_id'=>null]
      );
      // si llega cookie con items previos, puedes fusionar aquí (opcional)
      return $cart;
    }

    // invitado => cookie_id
    $cookieId = Cookie::get(self::COOKIE_KEY);
    if (!$cookieId) {
      $cookieId = Str::uuid()->toString();
      Cookie::queue(self::COOKIE_KEY, $cookieId, self::COOKIE_TTL_MIN);
    }
    return Cart::firstOrCreate(['cookie_id'=>$cookieId, 'status'=>'open']);
  }

  public function addItem(array $payload): CartItem {
    $cart = $this->current();

    // payload esperado: product_id, qty, options (map code => array ids), notes (opt)
    $qty     = max(1, (int)($payload['qty'] ?? 1));
    $config  = [
      'options' => $payload['options'] ?? [],
      'notes'   => $payload['notes']   ?? null,
    ];

    // (Opcional) lógica para precio unitario según config
    $unit = null; $line = null;

    return $cart->items()->create([
      'product_id' => $payload['product_id'],
      'qty'        => $qty,
      'config_json'=> $config,
      'unit_price' => $unit,
      'line_total' => $line,
    ]);
  }

  public function summary(): array {
    $cart = $this->current()->load(['items.product']);
    $subtotal = 0.0;
    foreach ($cart->items as $it) {
      $subtotal += ($it->line_total ?? 0);
    }
    return [
      'cart_id'  => $cart->id,
      'items'    => $cart->items->map(function($it){
        return [
          'id'      => $it->id,
          'pid'     => $it->product_id,
          'name'    => $it->product->name,
          'qty'     => $it->qty,
          'config'  => $it->config_json,
          'photo'   => $it->product->primary_photo['url'] ?? null,
          'unit'    => $it->unit_price,
          'total'   => $it->line_total,
        ];
      })->values(),
      'subtotal' => $subtotal,
      'tax'      => round($subtotal * 0.19, 2),
      'total'    => round($subtotal * 1.19, 2),
    ];
  }

  public function removeItem(int $itemId): void {
    $cart = $this->current();
    $cart->items()->where('id',$itemId)->delete();
  }

  public function updateQty(int $itemId, int $qty): void {
    $cart = $this->current();
    $cart->items()->where('id',$itemId)->update(['qty'=>max(1,$qty)]);
  }
}
