<?php

use Transbank\Webpay\WebpayPlus\Transaction;

class PayController extends Controller
{
    public function create(Request $request)
    {
        $amount     = (int) $request->input('amount');              // total en CLP
        $buyOrder   = 'BO-' . now()->timestamp . '-' . Str::random(6); // único!
        $sessionId  = (string) auth()->id() ?: Str::uuid();
        $returnUrl  = route('pay.return'); // Debe ser pública (https)

        $tx = new Transaction();
        $res = $tx->create($buyOrder, $sessionId, $amount, $returnUrl);

        // Redirige al formulario de Webpay (token + URL)
        return redirect()->away($res->getUrl() . '?token_ws=' . $res->getToken());
    }

    public function return(Request $request)
    {
        $token = $request->input('token_ws');
        abort_unless($token, 400, 'Token faltante');

        $tx = new Transaction();
        $res = $tx->commit($token);

        // $res->getStatus() === 'AUTHORIZED' indica éxito; guarda en DB, vacía carrito, etc.
        // Muestra voucher propio (en REST ya no hay "voucher" de Transbank).
        return view('store.payment-result', ['res' => $res]);
    }
}
