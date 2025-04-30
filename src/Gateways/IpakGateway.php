<?php

namespace Lipe\Payment\Gateways;

class IpakGateway extends AbstractGateway
{
    protected string $baseUrl = 'https://ipak.uz/api/';

    const STATE_CREATED = 0;
    const STATE_PAID = 1;
    const STATE_CANCELLED = 2;
    const STATE_ERROR = 3;

    public function createTransaction(string $orderId, float $amount, array $params = []): array
    {
        if (!$this->validateAmount($amount)) {
            $this->handleError('invalid_amount', 'Amount must be greater than 0');
        }

        $transactionId = $this->generateTransactionId();
        $data = [
            'merchant_id' => $this->merchantId,
            'order_id' => $orderId,
            'amount' => $amount,
            'currency' => 'UZS',
            'description' => $params['description'] ?? 'Payment for order ' . $orderId,
            'callback_url' => $params['callback_url'] ?? '',
            'return_url' => $params['return_url'] ?? '',
            'signature' => $this->generateSignature([
                'merchant_id' => $this->merchantId,
                'order_id' => $orderId,
                'amount' => $amount,
            ]),
        ];

        $response = $this->sendRequest('POST', 'payment/create', $data);
        $this->logTransaction($response);

        return [
            'transaction_id' => $transactionId,
            'payment_url' => $this->getPaymentUrl($transactionId),
            'response' => $response
        ];
    }

    public function checkTransaction(string $transactionId): array
    {
        $data = [
            'merchant_id' => $this->merchantId,
            'transaction_id' => $transactionId,
            'signature' => $this->generateSignature([
                'merchant_id' => $this->merchantId,
                'transaction_id' => $transactionId,
            ]),
        ];

        $response = $this->sendRequest('POST', 'payment/status', $data);
        $this->logTransaction($response);

        return $response;
    }

    public function cancelTransaction(string $transactionId): array
    {
        $data = [
            'merchant_id' => $this->merchantId,
            'transaction_id' => $transactionId,
            'signature' => $this->generateSignature([
                'merchant_id' => $this->merchantId,
                'transaction_id' => $transactionId,
            ]),
        ];

        $response = $this->sendRequest('POST', 'payment/cancel', $data);
        $this->logTransaction($response);

        return $response;
    }

    public function getPaymentUrl(string $transactionId): string
    {
        return "https://ipak.uz/payment/{$transactionId}";
    }

    public function verifySignature(array $data): bool
    {
        if (!isset($data['signature'])) {
            return false;
        }

        $signature = $data['signature'];
        unset($data['signature']);

        $expectedSignature = $this->generateSignature($data);
        return hash_equals($expectedSignature, $signature);
    }

    public function generateSignature(array $data): string
    {
        ksort($data);
        $dataString = implode('|', $data);
        return hash_hmac('sha256', $dataString, $this->merchantKey);
    }
}
