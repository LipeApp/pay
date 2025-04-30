<?php

namespace UzPaymentGateways\Examples;

use gateways\src\DTO\PaymentDTO;
use gateways\src\DTO\PaymentResponseDTO;
use gateways\src\DTO\PaymentStatusDTO;
use gateways\src\Gateways\PaymentGatewayFactory;
use gateways\src\Exceptions\PaymentException;

class PaymentService
{
    public function processPayment(PaymentDTO $paymentDTO, string $gateway = 'payme'): PaymentResponseDTO
    {
        try {
            $gateway = PaymentGatewayFactory::create($gateway, [
                'merchant_id' => env(strtoupper($gateway) . '_MERCHANT_ID'),
                'merchant_key' => env(strtoupper($gateway) . '_MERCHANT_KEY'),
            ]);

            return $gateway->createTransaction($paymentDTO);

        } catch (PaymentException $e) {
            return new PaymentResponseDTO(
                transactionId: '',
                paymentUrl: '',
                response: [],
                error: $e->getMessage(),
                errorCode: $e->getCode()
            );
        }
    }

    public function checkPaymentStatus(string $transactionId, string $gateway = 'payme'): PaymentStatusDTO
    {
        try {
            $gateway = PaymentGatewayFactory::create($gateway, [
                'merchant_id' => env(strtoupper($gateway) . '_MERCHANT_ID'),
                'merchant_key' => env(strtoupper($gateway) . '_MERCHANT_KEY'),
            ]);

            return $gateway->checkTransaction($transactionId);

        } catch (PaymentException $e) {
            return new PaymentStatusDTO(
                transactionId: $transactionId,
                status: 0,
                error: $e->getMessage(),
                errorCode: $e->getCode()
            );
        }
    }

    public function cancelPayment(string $transactionId, string $gateway = 'payme'): PaymentStatusDTO
    {
        try {
            $gateway = PaymentGatewayFactory::create($gateway, [
                'merchant_id' => env(strtoupper($gateway) . '_MERCHANT_ID'),
                'merchant_key' => env(strtoupper($gateway) . '_MERCHANT_KEY'),
            ]);

            return $gateway->cancelTransaction($transactionId);

        } catch (PaymentException $e) {
            return new PaymentStatusDTO(
                transactionId: $transactionId,
                status: 0,
                error: $e->getMessage(),
                errorCode: $e->getCode()
            );
        }
    }
}
