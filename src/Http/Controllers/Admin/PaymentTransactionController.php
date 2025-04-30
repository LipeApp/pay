<?php

namespace UzPaymentGateways\Http\Controllers\Admin;

use UzPaymentGateways\Http\Controllers\Controller;
use gateways\src\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentTransaction::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest()->paginate(20);

        return view('payment-gateways::admin.transactions.index', compact('transactions'));
    }

    public function show(PaymentTransaction $transaction)
    {
        return view('payment-gateways::admin.transactions.show', compact('transaction'));
    }
}
