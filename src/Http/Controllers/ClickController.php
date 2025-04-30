<?php

namespace Lipe\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Lipe\Payment\Gateways\ClickGateway;
use Lipe\Payment\Models\PaymentTransaction;

class ClickController extends Controller
{
    protected ClickGateway $gateway;

    public function __construct(ClickGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Подготовка платежа (Prepare)
     * Action = 0
     */
    public function prepare(Request $request)
    {
        Log::info("CLICK-LOG-PREPARE: " . $request->getContent());

        $transaction = PaymentTransaction::findByOrderId($request->input('merchant_trans_id'));

        if (!$transaction) {
            return response()->json([
                'error' => -5,
                'error_note' => 'Transaction not found'
            ]);
        }

        $result = $this->gateway->prepare($request->all());

        return response()->json($result);
    }

    /**
     * Завершение платежа (Complete)
     * Action = 1
     */
    public function complete(Request $request)
    {
        Log::info("CLICK-LOG-COMPLETE: " . $request->getContent());

        $transaction = PaymentTransaction::findByOrderId($request->input('merchant_trans_id'));

        if (!$transaction) {
            return response()->json([
                'error' => -5,
                'error_note' => 'Transaction not found'
            ]);
        }

        $result = $this->gateway->complete($request->all());

        return response()->json($result);
    }
}
