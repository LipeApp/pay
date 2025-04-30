<?php

namespace UzPaymentGateways\DTO;

class PaymentStatusDTO
{
    public function __construct(
        public readonly string $transactionId,
        public readonly int $status,
        public readonly ?string $statusText = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $error = null,
        public readonly ?string $errorCode = null,
        public readonly array $additionalData = []
    ) {}

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function isPaid(): bool
    {
        return $this->status === 2; // Пример статуса для успешной оплаты
    }

    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'status' => $this->status,
            'status_text' => $this->statusText,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'error' => $this->error,
            'error_code' => $this->errorCode,
            ...$this->additionalData
        ];
    }
} 