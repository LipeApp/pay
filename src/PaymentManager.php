<?php

namespace Lipe\Payment;

use Illuminate\Contracts\Foundation\Application;
use Lipe\Payment\Contracts\PaymentGatewayInterface;
use Lipe\Payment\Gateways\ClickGateway;
use Lipe\Payment\Gateways\IpakYuliGateway;
use Lipe\Payment\Gateways\PaymeGateway;

class PaymentManager
{
    protected Application $app;
    protected array $gateways = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Получить платежный шлюз
     *
     * @param string $name
     * @return PaymentGatewayInterface
     */
    public function gateway(string $name): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->createGateway($name);
        }

        return $this->gateways[$name];
    }

    /**
     * Создать платежный шлюз
     *
     * @param string $name
     * @return PaymentGatewayInterface
     */
    protected function createGateway(string $name): PaymentGatewayInterface
    {
        $config = config("payment-gateways.gateways.{$name}");

        if (!$config) {
            throw new \InvalidArgumentException("Gateway [{$name}] not found.");
        }

        return match ($name) {
            'payme' => new PaymeGateway($config),
            'click' => new ClickGateway($config),
            'ipak_yuli' => new IpakYuliGateway($config),
            default => throw new \InvalidArgumentException("Gateway [{$name}] not supported."),
        };
    }

    /**
     * Получить список доступных шлюзов
     *
     * @return array
     */
    public function getAvailableGateways(): array
    {
        return array_keys(config('payment-gateways.gateways'));
    }

    /**
     * Получить шлюз по умолчанию
     *
     * @return PaymentGatewayInterface
     */
    public function getDefaultGateway(): PaymentGatewayInterface
    {
        $default = config('payment-gateways.default');
        return $this->gateway($default);
    }
}
