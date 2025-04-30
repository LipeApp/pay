<?php

namespace Lipe\Payment\Gateways;

use Lipe\Payment\Exceptions\PaymentException;

class PaymentGatewayFactory
{
    /**
     * Создать экземпляр платежного шлюза
     *
     * @param string $gatewayName
     * @param array $config
     * @return AbstractGateway
     * @throws PaymentException
     */
    public static function create(string $gatewayName, array $config): AbstractGateway
    {
        if (!isset($config['merchant_id']) || !isset($config['merchant_key'])) {
            throw new PaymentException('Missing required configuration parameters', 'config_error');
        }

        return match ($gatewayName) {
            'payme' => new PaymeGateway($config),
            'click' => new ClickGateway($config),
            'ipak' => new IpakGateway($config),
            default => throw new PaymentException("Unknown gateway: $gatewayName", 'unknown_gateway'),
        };
    }
}
