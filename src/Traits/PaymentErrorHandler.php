<?php

namespace UzPaymentGateways\Traits;

use gateways\src\Exceptions\PaymentException;

trait PaymentErrorHandler
{
    /**
     * Обработка ошибок
     *
     * @param string $code
     * @param string $message
     * @throws gateways\src\Exceptions\PaymentException
     */
    protected function handleError(string $code, string $message): void
    {
        throw new PaymentException($message, $code);
    }

    /**
     * Логирование транзакции
     *
     * @param array $data
     * @return void
     */
    protected function logTransaction(array $data): void
    {
        // Здесь можно добавить логирование в файл или базу данных
        if (function_exists('logger')) {
            logger()->info('Payment transaction', $data);
        }
    }
}
