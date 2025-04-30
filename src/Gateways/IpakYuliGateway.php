<?php

namespace Lipe\Payment\Gateways;

use Lipe\Payment\Contracts\PaymentGatewayInterface;
use Lipe\Payment\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;

class IpakYuliGateway extends BaseGateway implements PaymentGatewayInterface
{
    public function init(array $params): array
    {
        $this->log('Инициализация платежа Ipak Yuli', $params);

        if (!$this->validate($params)) {
            return $this->createErrorResponse('Invalid parameters');
        }

        $transaction = $this->createTransaction($params);

        // Получаем токен авторизации
        $authResponse = $this->getAuthToken();
        if (!$authResponse['success']) {
            return $this->createErrorResponse('Authentication failed');
        }

        // Создаем платеж
        $paymentResponse = $this->createPayment($authResponse['token'], $transaction);
        if (!$paymentResponse['success']) {
            return $this->createErrorResponse('Payment creation failed');
        }

        return [
            'success' => true,
            'transaction_id' => $transaction->id,
            'payment_url' => $paymentResponse['payment_url'],
            'amount' => $params['amount'],
            'order_id' => $params['order_id'],
        ];
    }

    public function check(array $params): array
    {
        $this->log('Проверка платежа Ipak Yuli', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['transaction_id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        return [
            'success' => true,
            'transaction_id' => $transaction->params['transaction_id'],
            'order_id' => $transaction->order_id,
            'status' => $transaction->status,
        ];
    }

    public function confirm(array $params): array
    {
        $this->log('Подтверждение платежа Ipak Yuli', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['transaction_id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        $this->updateTransactionStatus(PaymentTransaction::STATUS_COMPLETED, [
            'perform_time' => time(),
        ]);

        return [
            'success' => true,
            'transaction_id' => $transaction->params['transaction_id'],
            'order_id' => $transaction->order_id,
            'status' => PaymentTransaction::STATUS_COMPLETED,
        ];
    }

    public function cancel(array $params): array
    {
        $this->log('Отмена платежа Ipak Yuli', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['transaction_id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        $this->updateTransactionStatus(PaymentTransaction::STATUS_CANCELLED, [
            'cancel_time' => time(),
            'reason' => $params['reason'] ?? null,
        ]);

        return [
            'success' => true,
            'transaction_id' => $transaction->params['transaction_id'],
            'order_id' => $transaction->order_id,
            'status' => PaymentTransaction::STATUS_CANCELLED,
        ];
    }

    public function status(array $params): array
    {
        $this->log('Получение статуса платежа Ipak Yuli', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['transaction_id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        return [
            'success' => true,
            'transaction_id' => $transaction->params['transaction_id'],
            'order_id' => $transaction->order_id,
            'status' => $transaction->status,
            'create_time' => $transaction->created_at->timestamp,
            'perform_time' => $transaction->updated_at->timestamp,
            'cancel_time' => $transaction->params['cancel_time'] ?? null,
        ];
    }

    public function validate(array $params): bool
    {
        return isset($params['amount'], $params['order_id']);
    }

    public function generateSignature(array $params): string
    {
        $data = [
            $params['transaction_id'] ?? '',
            $params['amount'] ?? '',
            $params['order_id'] ?? '',
            $this->config['secret_key'],
        ];

        return md5(implode('', $data));
    }

    protected function verifySignature(array $params): bool
    {
        $signature = $params['signature'] ?? '';
        $generatedSignature = $this->generateSignature($params);

        return hash_equals($signature, $generatedSignature);
    }

    protected function getGatewayName(): string
    {
        return 'ipak_yuli';
    }

    protected function createErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => [
                'code' => -1,
                'message' => $message,
            ],
        ];
    }

    private function getAuthToken(): array
    {
        $response = Http::post($this->config['auth_url'], [
            'login' => $this->config['login'],
            'password' => $this->config['password'],
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'token' => $response->json('token'),
            ];
        }

        return [
            'success' => false,
            'error' => $response->json('error'),
        ];
    }

    private function createPayment(string $token, PaymentTransaction $transaction): array
    {
        $response = Http::withToken($token)->post($this->config['transfer_url'], [
            'amount' => $transaction->amount,
            'order_id' => $transaction->order_id,
            'cashbox_id' => $this->config['cashbox_id'],
            'success_url' => $this->config['success_url'],
            'fail_url' => $this->config['fail_url'],
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'payment_url' => $response->json('payment_url'),
            ];
        }

        return [
            'success' => false,
            'error' => $response->json('error'),
        ];
    }
}
