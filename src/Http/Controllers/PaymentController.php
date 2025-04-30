<?php

namespace Lipe\Payment\Http\Controllers;

use Illuminate\Http\Request;
use Lipe\Payment\PaymentManager;

class PaymentController
{
    protected PaymentManager $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    public function create(Request $request)
    {
        $gateway = $request->input('gateway', config('payment-gateways.default'));
        $amount = $request->input('amount');
        $currency = $request->input('currency', 'UZS');
        $description = $request->input('description');
        $metadata = $request->input('metadata', []);

        $payment = $this->paymentManager->gateway($gateway)
            ->createPayment($amount, $currency, $description, $metadata);

        return response()->json($payment);
    }

    public function callback(Request $request, string $gateway)
    {
        $response = $this->paymentManager->gateway($gateway)
            ->handleCallback($request->all());

        return response()->json($response);
    }

    public function status(string $gateway, string $paymentId)
    {
        $status = $this->paymentManager->gateway($gateway)
            ->getPaymentStatus($paymentId);

        return response()->json($status);
    }
} 