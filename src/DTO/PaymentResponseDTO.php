<?php

namespace UzPaymentGateways\DTO;

class PaymentResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $transactionId = null,
        public readonly ?string $paymentUrl = null,
        public readonly ?array $error = null,
        public readonly ?array $additionalData = null,
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'transaction_id' => $this->transactionId,
            'payment_url' => $this->paymentUrl,
            'error' => $this->error,
            ...($this->additionalData ?? [])
        ];
    }
} 