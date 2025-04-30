<?php

namespace Lipe\Payment\Gateways;

use Lipe\Payment\Contracts\PaymentGatewayInterface;
use Lipe\Payment\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;

class PaymeGateway extends BaseGateway implements PaymentGatewayInterface
{
    protected const TIMEOUT = 43200000;
    protected const STATE_CREATED = 1;
    protected const STATE_COMPLETED = 2;
    protected const STATE_CANCELLED = -1;
    protected const STATE_CANCELLED_AFTER_COMPLETE = -2;

    const CURRENCY_CODE_UZS = 860;
    const CURRENCY_CODE_RUB = 643;
    const CURRENCY_CODE_USD = 840;
    const CURRENCY_CODE_EUR = 978;

    public function init(array $params): array
    {
        $this->log('Инициализация платежа Payme', $params);

        if (!$this->validate($params)) {
            return $this->createErrorResponse('Invalid parameters');
        }

        $transaction = $this->createTransaction($params);

        return [
            'success' => true,
            'transaction_id' => $transaction->id,
            'merchant_id' => $this->config['merchant_id'],
            'amount' => $params['amount'],
            'order_id' => $params['order_id'],
        ];
    }

    public function check(array $params): array
    {
        $this->log('Проверка платежа Payme', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        return [
            'success' => true,
            'transaction' => $transaction->id,
            'state' => $transaction->status,
            'create_time' => $transaction->created_at->timestamp,
            'perform_time' => $transaction->updated_at->timestamp,
        ];
    }

    public function confirm(array $params): array
    {
        $this->log('Подтверждение платежа Payme', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        $this->updateTransactionStatus(PaymentTransaction::STATUS_COMPLETED, [
            'perform_time' => time(),
        ]);

        return [
            'success' => true,
            'transaction' => $transaction->id,
            'state' => PaymentTransaction::STATUS_COMPLETED,
            'perform_time' => time(),
        ];
    }

    public function cancel(array $params): array
    {
        $this->log('Отмена платежа Payme', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        $this->updateTransactionStatus(PaymentTransaction::STATUS_CANCELLED, [
            'cancel_time' => time(),
            'reason' => $params['reason'] ?? null,
        ]);

        return [
            'success' => true,
            'transaction' => $transaction->id,
            'state' => PaymentTransaction::STATUS_CANCELLED,
            'cancel_time' => time(),
        ];
    }

    public function status(array $params): array
    {
        $this->log('Получение статуса платежа Payme', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        return [
            'success' => true,
            'transaction' => $transaction->id,
            'state' => $transaction->status,
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
            $params['id'] ?? '',
            $params['time'] ?? '',
            $params['amount'] ?? '',
            $params['account'] ?? [],
            $this->config['merchant_key'],
        ];

        return hash_hmac('sha256', implode('', $data), $this->config['merchant_key']);
    }

    protected function verifySignature(array $params): bool
    {
        $signature = $params['signature'] ?? '';
        $generatedSignature = $this->generateSignature($params);

        return hash_equals($signature, $generatedSignature);
    }

    protected function getGatewayName(): string
    {
        return 'payme';
    }

    protected function createErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => [
                'code' => -31000,
                'message' => $message,
            ],
        ];
    }

    public function createTransaction(string $orderId, float $amount, array $params = []): array
    {
        if (!$this->validateAmount($amount)) {
            $this->handleError('invalid_amount', 'Amount must be greater than 0');
        }

        $transactionId = $this->generateTransactionId();
        $data = [
            'method' => 'receipts.create',
            'params' => [
                'amount' => $amount * 100, // Конвертация в тийины
                'account' => [
                    'order_id' => $orderId
                ],
                'currency' => self::CURRENCY_CODE_UZS,
                'merchant_id' => $this->merchantId,
                'transaction_id' => $transactionId,
            ]
        ];

        $response = $this->sendRequest('POST', 'receipts.create', $data);
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
            'method' => 'receipts.check',
            'params' => [
                'transaction_id' => $transactionId,
                'merchant_id' => $this->merchantId,
            ]
        ];

        $response = $this->sendRequest('POST', 'receipts.check', $data);
        $this->logTransaction($response);

        return $response;
    }

    public function cancelTransaction(string $transactionId): array
    {
        $data = [
            'method' => 'receipts.cancel',
            'params' => [
                'transaction_id' => $transactionId,
                'merchant_id' => $this->merchantId,
            ]
        ];

        $response = $this->sendRequest('POST', 'receipts.cancel', $data);
        $this->logTransaction($response);

        return $response;
    }

    public function getPaymentUrl(string $transactionId): string
    {
        return "https://checkout.paycom.uz/{$transactionId}";
    }
}
