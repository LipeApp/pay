<?php

namespace Lipe\Payment\Gateways;

use GuzzleHttp\Client;
use Lipe\Payment\Contracts\PaymentGatewayInterface;
use Lipe\Payment\Traits\PaymentErrorHandler;

abstract class AbstractGateway implements PaymentGatewayInterface
{
    use PaymentErrorHandler;

    protected Client $client;
    protected array $config;
    protected string $baseUrl;
    protected string $merchantId;
    protected string $merchantKey;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
        ]);
        $this->merchantId = $config['merchant_id'];
        $this->merchantKey = $config['merchant_key'];
    }

    /**
     * Генерация уникального ID транзакции
     *
     * @return string
     */
    protected function generateTransactionId(): string
    {
        return uniqid($this->merchantId . '_', true);
    }

    /**
     * Валидация суммы
     *
     * @param float $amount
     * @return bool
     */
    protected function validateAmount(float $amount): bool
    {
        return $amount > 0;
    }

    /**
     * Отправка HTTP запроса
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function sendRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = $this->client->request($method, $endpoint, [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->handleError('request_failed', $e->getMessage());
            return [];
        }
    }
}
