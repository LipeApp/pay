<?php

namespace Lipe\Payment\DTO;

class PaymentDTO
{
    public function __construct(
        public readonly string $orderId,
        public readonly float $amount,
        public readonly array $params = [],
        public readonly ?string $currency = null,
        public readonly ?string $description = null,
        public readonly ?string $callbackUrl = null,
        public readonly ?string $returnUrl = null,
    ) {}

    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'callback_url' => $this->callbackUrl,
            'return_url' => $this->returnUrl,
            ...$this->params
        ];
    }
}
