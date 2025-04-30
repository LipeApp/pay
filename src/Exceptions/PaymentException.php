<?php

namespace UzPaymentGateways\Exceptions;

class PaymentException extends \Exception
{
    protected array $errors = [
        'invalid_amount' => 'Неверная сумма платежа',
        'invalid_order' => 'Неверный номер заказа',
        'invalid_signature' => 'Неверная подпись',
        'request_failed' => 'Ошибка при отправке запроса',
        'transaction_not_found' => 'Транзакция не найдена',
        'transaction_expired' => 'Время действия транзакции истекло',
        'insufficient_funds' => 'Недостаточно средств',
        'system_error' => 'Системная ошибка',
    ];

    public function __construct(string $message = '', string $code = 'system_error', int $previous = null)
    {
        if (empty($message) && isset($this->errors[$code])) {
            $message = $this->errors[$code];
        }

        parent::__construct($message, 0, $previous);
    }
} 