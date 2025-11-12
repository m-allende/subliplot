<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

class WebpayHttpService
{
    private Client $http;
    private string $base;
    private string $commerceCode;
    private string $apiKey;

    public function __construct()
    {
        $env = config('services.transbank.env', 'integration');

        if ($env === 'production') {
            // Producción (pon tus credenciales reales en .env)
            $this->base         = 'https://webpay3g.transbank.cl';
            $this->commerceCode = (string) config('services.transbank.commerce_code');
            $this->apiKey       = (string) config('services.transbank.api_key');
        } else {
            // Integración
            $this->base         = 'https://webpay3gint.transbank.cl';
            $this->commerceCode = '597055555532';
            $this->apiKey       = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C';
        }

        $this->http = new Client([
            'base_uri' => $this->base,
            'timeout'  => 15,
        ]);
    }

    /** Crea transacción Webpay Plus */
    public function create(int $amount, string $returnUrl, ?string $sessionId = null): array
    {
        try {
            $buyOrder  = 'BO-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));
            $sessionId = $sessionId ?: (string) (auth()->id() ?? Str::uuid());

            $resp = $this->http->post('/rswebpaytransaction/api/webpay/v1.2/transactions', [
                'headers' => [
                    'Tbk-Api-Key-Id'     => $this->commerceCode,
                    'Tbk-Api-Key-Secret' => $this->apiKey,
                    'Content-Type'       => 'application/json',
                ],
                'json' => [
                    'buy_order'  => $buyOrder,
                    'session_id' => $sessionId,
                    'amount'     => $amount,
                    'return_url' => $returnUrl,
                ],
            ]);

            $json = json_decode((string) $resp->getBody(), true);

            return [
                'token'      => $json['token'] ?? null,
                'url' => $json['url']   ?? null, // /.../webpay/v1.2/transactions
                'buy_order'  => $buyOrder,
                'session_id' => $sessionId,
                'amount'     => $amount,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
        
    }

    /** Confirma (commit) la transacción usando el token */
    public function commit(string $token): array
    {
        $resp = $this->http->put("/rswebpaytransaction/api/webpay/v1.2/transactions/{$token}", [
            'headers' => [
                'Tbk-Api-Key-Id'     => $this->commerceCode,
                'Tbk-Api-Key-Secret' => $this->apiKey,
                'Content-Type'       => 'application/json',
            ],
        ]);

        return json_decode((string) $resp->getBody(), true);
    }
}
