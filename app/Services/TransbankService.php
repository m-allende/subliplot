<?php
namespace App\Services;

use Transbank\Webpay\WebpayPlus\Transaction;
use Transbank\Webpay\WebpayPlus\WebpayPlus;
use Transbank\Webpay\Options;
use Transbank\Webpay\WebpayPlus\IntegrationCommerceCodes;
use Transbank\Webpay\WebpayPlus\IntegrationApiKeys;

class TransbankService {
    
    WebpayPlus::setCommerceCode(IntegrationCommerceCodes::WEBPAY_PLUS);
    WebpayPlus::setApiKey(IntegrationApiKeys::WEBPAY);
    WebpayPlus::setIntegrationType(\Transbank\Webpay\Enum\Environment::INTEGRATION);
}

