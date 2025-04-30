<?php

namespace Lipe\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';

    protected $fillable = [
        'gateway',
        'amount',
        'currency',
        'status',
        'transaction_id',
        'payment_id',
        'description',
        'metadata',
        'error_message',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Статусы транзакций
    public const STATUS_CREATED = 'created';
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    /**
     * Получить транзакцию по ID заказа
     *
     * @param string $orderId
     * @return self|null
     */
    public static function findByOrderId(string $orderId): ?self
    {
        return static::where('order_id', $orderId)->first();
    }

    /**
     * Получить транзакцию по ID транзакции платежной системы
     *
     * @param string $transactionId
     * @return self|null
     */
    public static function findByTransactionId(string $transactionId): ?self
    {
        return static::where('params->transaction_id', $transactionId)->first();
    }

    /**
     * Обновить статус транзакции
     *
     * @param string $status
     * @param array|null $params
     * @return bool
     */
    public function updateStatus(string $status, ?array $params = null): bool
    {
        $this->status = $status;
        if ($params) {
            $this->params = array_merge($this->params, $params);
        }
        return $this->save();
    }

    /**
     * Установить сообщение об ошибке
     *
     * @param string $message
     * @return bool
     */
    public function setError(string $message): bool
    {
        $this->error_message = $message;
        $this->status = self::STATUS_FAILED;
        return $this->save();
    }
}
