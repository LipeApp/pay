<?php

namespace Lipe\Payment\Gateways;

use Lipe\Payment\Contracts\PaymentGatewayInterface;
use Lipe\Payment\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;

abstract class BaseGateway implements PaymentGatewayInterface
{
    protected array $config;
    protected PaymentTransaction $transaction;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Создать новую транзакцию
     *
     * @param array $params
     * @return Lipe\Payment\Models\PaymentTransaction
     */
    protected function createTransaction(array $params): PaymentTransaction
    {
        $this->transaction = new PaymentTransaction();
        $this->transaction->gateway = $this->getGatewayName();
        $this->transaction->amount = $params['amount'] ?? 0;
        $this->transaction->order_id = $params['order_id'] ?? null;
        $this->transaction->status = PaymentTransaction::STATUS_CREATED;
        $this->transaction->params = $params;
        $this->transaction->save();

        return $this->transaction;
    }

    /**
     * Обновить статус транзакции
     *
     * @param string $status
     * @param array|null $params
     * @return void
     */
    protected function updateTransactionStatus(string $status, ?array $params = null): void
    {
        if ($this->transaction) {
            $this->transaction->status = $status;
            if ($params) {
                $this->transaction->params = array_merge($this->transaction->params, $params);
            }
            $this->transaction->save();
        }
    }

    /**
     * Логирование
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function log(string $message, array $context = []): void
    {
        Log::channel('payment')->info($message, array_merge([
            'gateway' => $this->getGatewayName(),
            'transaction_id' => $this->transaction->id ?? null,
        ], $context));
    }

    /**
     * Получить имя платежного шлюза
     *
     * @return string
     */
    abstract protected function getGatewayName(): string;

    /**
     * Проверить подпись
     *
     * @param array $params
     * @return bool
     */
    abstract protected function verifySignature(array $params): bool;
}
