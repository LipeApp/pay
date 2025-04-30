<?php

namespace UzPaymentGateways\Contracts;

use gateways\src\DTO\PaymentDTO;
use gateways\src\DTO\PaymentResponseDTO;
use gateways\src\DTO\PaymentStatusDTO;

interface PaymentGatewayInterface
{
    /**
     * Инициализация платежа
     *
     * @param array $params
     * @return array
     */
    public function init(array $params): array;

    /**
     * Проверка платежа
     *
     * @param array $params
     * @return array
     */
    public function check(array $params): array;

    /**
     * Подтверждение платежа
     *
     * @param array $params
     * @return array
     */
    public function confirm(array $params): array;

    /**
     * Отмена платежа
     *
     * @param array $params
     * @return array
     */
    public function cancel(array $params): array;

    /**
     * Получение статуса платежа
     *
     * @param array $params
     * @return array
     */
    public function status(array $params): array;

    /**
     * Валидация входящих данных
     *
     * @param array $params
     * @return bool
     */
    public function validate(array $params): bool;

    /**
     * Генерация подписи
     *
     * @param array $params
     * @return string
     */
    public function generateSignature(array $params): string;

    /**
     * Создать новую транзакцию
     *
     * @param gateways\src\DTO\PaymentDTO $paymentDTO
     * @return gateways\src\DTO\PaymentResponseDTO
     */
    public function createTransaction(PaymentDTO $paymentDTO): PaymentResponseDTO;

    /**
     * Проверить статус транзакции
     *
     * @param string $transactionId
     * @return gateways\src\DTO\PaymentStatusDTO
     */
    public function checkTransaction(string $transactionId): PaymentStatusDTO;

    /**
     * Отменить транзакцию
     *
     * @param string $transactionId
     * @return gateways\src\DTO\PaymentStatusDTO
     */
    public function cancelTransaction(string $transactionId): PaymentStatusDTO;

    /**
     * Получить URL для оплаты
     *
     * @param string $transactionId
     * @return string
     */
    public function getPaymentUrl(string $transactionId): string;

    /**
     * Проверить подпись запроса
     *
     * @param array $data
     * @return bool
     */
    public function verifySignature(array $data): bool;

    public function getName(): string;

    public function initialize(PaymentDTO $payment): PaymentResponseDTO;

    public function processCallback(array $data): PaymentResponseDTO;

    public function checkStatus(string $transactionId): PaymentResponseDTO;

    public function cancel(string $transactionId): PaymentResponseDTO;
}
