<?php

namespace Lipe\Payment\Gateways;

use Lipe\Payment\Contracts\PaymentGatewayInterface;
use Lipe\Payment\Models\PaymentTransaction;

class ClickGateway extends BaseGateway implements PaymentGatewayInterface
{
    public function init(array $params): array
    {
        $this->log('Инициализация платежа Click', $params);

        if (!$this->validate($params)) {
            return $this->createErrorResponse('Invalid parameters');
        }

        $transaction = $this->createTransaction($params);

        return [
            'success' => true,
            'transaction_id' => $transaction->id,
            'merchant_id' => $this->config['merchant_id'],
            'merchant_user_id' => $this->config['merchant_user_id'],
            'service_id' => $this->config['service_id'],
            'amount' => $params['amount'],
            'order_id' => $params['order_id'],
        ];
    }

    public function check(array $params): array
    {
        $this->log('Проверка платежа Click', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['click_trans_id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        return [
            'success' => true,
            'click_trans_id' => $transaction->params['click_trans_id'],
            'merchant_trans_id' => $transaction->order_id,
            'merchant_prepare_id' => $transaction->id,
            'error' => 0,
            'error_note' => '',
        ];
    }

    public function confirm(array $params): array
    {
        $this->log('Подтверждение платежа Click', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['click_trans_id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        $this->updateTransactionStatus(PaymentTransaction::STATUS_COMPLETED, [
            'perform_time' => time(),
        ]);

        return [
            'success' => true,
            'click_trans_id' => $transaction->params['click_trans_id'],
            'merchant_trans_id' => $transaction->order_id,
            'merchant_confirm_id' => $transaction->id,
            'error' => 0,
            'error_note' => '',
        ];
    }

    public function cancel(array $params): array
    {
        $this->log('Отмена платежа Click', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['click_trans_id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        $this->updateTransactionStatus(PaymentTransaction::STATUS_CANCELLED, [
            'cancel_time' => time(),
            'reason' => $params['error_note'] ?? null,
        ]);

        return [
            'success' => true,
            'click_trans_id' => $transaction->params['click_trans_id'],
            'merchant_trans_id' => $transaction->order_id,
            'merchant_cancel_id' => $transaction->id,
            'error' => 0,
            'error_note' => '',
        ];
    }

    public function status(array $params): array
    {
        $this->log('Получение статуса платежа Click', $params);

        if (!$this->verifySignature($params)) {
            return $this->createErrorResponse('Invalid signature');
        }

        $transaction = PaymentTransaction::findByTransactionId($params['click_trans_id']);
        if (!$transaction) {
            return $this->createErrorResponse('Transaction not found');
        }

        return [
            'success' => true,
            'click_trans_id' => $transaction->params['click_trans_id'],
            'merchant_trans_id' => $transaction->order_id,
            'merchant_prepare_id' => $transaction->id,
            'error' => 0,
            'error_note' => '',
            'status' => $transaction->status,
        ];
    }

    public function validate(array $params): bool
    {
        return isset($params['amount'], $params['order_id']);
    }

    public function generateSignature(array $params): string
    {
        $data = [
            $params['click_trans_id'] ?? '',
            $params['service_id'] ?? '',
            $params['merchant_trans_id'] ?? '',
            $params['amount'] ?? '',
            $params['action'] ?? '',
            $params['sign_time'] ?? '',
            $this->config['secret_key'],
        ];

        return md5(implode('', $data));
    }

    protected function verifySignature(array $params): bool
    {
        $signature = $params['sign_string'] ?? '';
        $generatedSignature = $this->generateSignature($params);

        return hash_equals($signature, $generatedSignature);
    }

    protected function getGatewayName(): string
    {
        return 'click';
    }

    protected function createErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => -1,
            'error_note' => $message,
        ];
    }

    /**
     * Подготовка платежа (Prepare)
     * Action = 0
     */
    public function prepare(array $params): array
    {
        $this->validateParams($params, [
            'click_trans_id',
            'service_id',
            'merchant_trans_id',
            'amount',
            'action',
            'sign_time',
            'sign_string'
        ]);

        $result = $this->requestCheck($params);

        if ($result['error'] == 0) {
            $this->transaction->updateStatus(PaymentTransaction::STATUS_PENDING);
        }

        return $result;
    }

    /**
     * Завершение платежа (Complete)
     * Action = 1
     */
    public function complete(array $params): array
    {
        $this->validateParams($params, [
            'click_trans_id',
            'service_id',
            'merchant_trans_id',
            'merchant_prepare_id',
            'amount',
            'action',
            'sign_time',
            'sign_string'
        ]);

        $result = $this->requestCheck($params);

        if ($result['error'] == 0) {
            $this->transaction->updateStatus(PaymentTransaction::STATUS_COMPLETED);
        }

        return $result;
    }

    /**
     * Проверка запроса
     */
    protected function requestCheck(array $params): array
    {
        $signString = md5(
            $params['click_trans_id'] .
            $params['service_id'] .
            $this->config['merchant_id'] .
            $params['merchant_trans_id'] .
            $params['amount'] .
            $params['action'] .
            $params['sign_time'] .
            $this->config['merchant_key']
        );

        if ($signString != $params['sign_string']) {
            return [
                'error' => -1,
                'error_note' => 'SIGN CHECK FAILED!'
            ];
        }

        return [
            'error' => 0,
            'error_note' => 'Success'
        ];
    }
}
