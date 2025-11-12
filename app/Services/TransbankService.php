<?php

namespace App\Services;

use Illuminate\Support\Str;
// ¡OJO! SOLO importamos Transaction para no forzar el autoload de clases que quizá no existen.
use Transbank\Webpay\WebpayPlus\Transaction;

class TransbankService
{
    /** @var \Transbank\Webpay\WebpayPlus\Transaction */
    private $tx;

    public function __construct()
    {
        $env = config('services.transbank.env', 'integration');

        // ---- Rama 1: SDK v4 con clase WebpayPlus disponible (configureForIntegration / Production)
        if (class_exists(\Transbank\Webpay\WebpayPlus\WebpayPlus::class)) {
            if ($env === 'production') {
                $commerceCode = (string) config('services.transbank.commerce_code');
                $apiKey       = (string) config('services.transbank.api_key');
                \Transbank\Webpay\WebpayPlus\WebpayPlus::configureForProduction($commerceCode, $apiKey);
            } else {
                \Transbank\Webpay\WebpayPlus\WebpayPlus::configureForIntegration('597055555532', 'X');
            }
            $this->tx = new Transaction();
            return;
        }

        // ---- Rama 2: SDK con clase Options disponible (algunas 4.x)
        if (class_exists(\Transbank\Webpay\Options::class)) {
            if ($env === 'production') {
                $opts = new \Transbank\Webpay\Options(
                    (string) config('services.transbank.commerce_code'),
                    (string) config('services.transbank.api_key'),
                    'production' // sin enum; string para máxima compatibilidad
                );
            } else {
                $opts = new \Transbank\Webpay\Options('597055555532', 'X', 'integration');
            }
            $this->tx = new Transaction($opts);
            return;
        }

        // ---- Rama 3: SDK antiguo con Configuration (fallback extremo)
        if (class_exists(\Transbank\Webpay\Configuration::class)) {
            $cfg = new \Transbank\Webpay\Configuration();
            if ($env === 'production') {
                $cfg->setCommerceCode((string) config('services.transbank.commerce_code'));
                $cfg->setApiKey((string) config('services.transbank.api_key'));
                $cfg->setEnvironment('production');
            } else {
                $cfg->setCommerceCode('597055555532');
                $cfg->setApiKey('X');
                $cfg->setEnvironment('integration');
            }
            $this->tx = new Transaction($cfg);
            return;
        }

        // Si llegamos aquí, la instalación del SDK no expone ninguna de las clases esperadas.
        throw new \RuntimeException('Transbank SDK no compatible o mal instalado: faltan WebpayPlus/Options/Configuration.');
    }

    public function create(int $amount, string $returnUrl, ?string $sessionId = null): array
    {
        $buyOrder  = 'BO-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));
        $sessionId = $sessionId ?: (string)(auth()->id() ?? Str::uuid());

        $res = $this->tx->create($buyOrder, $sessionId, $amount, $returnUrl);

        return [
            'token'     => $res->getToken(),
            'url'       => $res->getUrl(),
            'buyOrder'  => $buyOrder,
            'sessionId' => $sessionId,
            'amount'    => $amount,
        ];
    }

    public function commit(string $token)
    {
        return $this->tx->commit($token);
    }
}
