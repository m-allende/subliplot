<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Services\TransbankService;

class WebpayController extends Controller
{
    public function __construct(protected TransbankService $tbk){}

    /** Total en CLP a partir de la sesión del carrito */
    protected function cartTotal(): int
    {
        $cart = session('cart', ['items' => [], 'currency' => 'CLP']);
        $items = $cart['items'] ?? [];

        // Si ya tienes cálculo de totales en otro lado, úsalo.
        $total = 0;
        foreach ($items as $it) {
            // Esperado: ['price' => int, 'qty' => int] o similar
            $price = (int)($it['price'] ?? 0);
            $qty   = (int)($it['qty'] ?? 1);
            $total += $price * $qty;
        }
        return (int) $total;
    }

    public function create(Request $request)
    {
        // 1) Validar carrito
        $cart = session('cart', ['items' => []]);
        if (empty($cart['items'])) {
            return back()->with('error', 'Tu carrito está vacío.');
        }

        // 2) Calcular total
        $amount = $this->cartTotal();
        if ($amount <= 0) {
            return back()->with('error', 'Total inválido para pago.');
        }

        // 3) Crear transacción
        $returnUrl = config('services.transbank.return_url');
        $data = $this->tbk->create($amount, $returnUrl, (string)auth()->id());

        // 4) Persistir “pre-orden” simple (opcional pero recomendado para trazabilidad)
        //    Crea una tabla 'payments' o 'orders' si quieres. Aquí solo guardamos en sesión.
        session([
            'tbk' => [
                'token'     => $data['token'],
                'buyOrder'  => $data['buyOrder'],
                'amount'    => $data['amount'],
            ]
        ]);

        // 5) Redirigir a Webpay
        return redirect()->away($data['url'].'?token_ws='.$data['token']);
    }

    public function return(Request $request)
    {
        $token = $request->input('token_ws');

        if (!$token) {
            return view('store.payment-result', [
                'ok' => false,
                'message' => 'Token no recibido desde Webpay.',
                'details' => null
            ]);
        }

        // 1) Confirmar transacción
        $res = $this->tbk->commit($token);

        // 2) Validaciones mínimas: comparar montos/orden con lo que guardaste
        $saved = session('tbk', []);
        $amountOk = ((int)($res->getAmount() ?? 0)) === ((int)($saved['amount'] ?? -1));
        $orderOk  = ($res->getBuyOrder() ?? '') === ($saved['buyOrder'] ?? '');

        // 3) Lógica post-pago (en éxito)
        $authorized = ($res->getStatus() === 'AUTHORIZED');

        if ($authorized && $amountOk && $orderOk) {
            // Aquí guarda tu “orden” definitiva y marca como pagada, etc.
            // Limpia carrito
            session()->forget('cart');
            $ok = true;
            $msg = 'Pago aprobado';
        } else {
            $ok = false;
            $msg = 'Pago rechazado o inválido';
        }

        // 4) Render resultado
        return view('store.payment-result', [
            'ok'      => $ok,
            'message' => $msg,
            'details' => $res,
        ]);
    }
}
