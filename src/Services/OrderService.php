<?php

namespace Lipe\Payment\Services;

use Lipe\Payment\Models\Basket;
use Lipe\Payment\Models\Order;
use Lipe\Payment\Models\OrderItem;
use Lipe\Payment\PaymentManager;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected PaymentManager $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    public function createOrder(Basket $basket, string $gateway = 'click'): Order
    {
        return DB::transaction(function () use ($basket, $gateway) {
            // Создаем заказ
            $order = Order::create([
                'user_id' => $basket->user_id,
                'basket_id' => $basket->id,
                'total_amount' => $basket->total_price,
                'status' => 'pending',
                'payment_gateway' => $gateway,
            ]);

            // Копируем товары из корзины в заказ
            foreach ($basket->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }

            // Создаем платеж через Click
            $payment = $this->paymentManager->gateway($gateway)->createPayment([
                'amount' => $order->total_amount,
                'order_id' => $order->id,
                'description' => 'Оплата заказа #' . $order->id,
            ]);

            // Обновляем заказ данными платежа
            $order->update([
                'payment_id' => $payment['payment_id'],
                'payment_url' => $payment['payment_url'],
            ]);

            return $order;
        });
    }
} 