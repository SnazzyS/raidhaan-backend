<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\Order;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if ($order->status === 'completed' && (
            $order->wasChanged('status')
            || $order->wasChanged('delivery_type')
            || $order->wasChanged('payment_method')
            || $order->wasChanged('subtotal_amount')
            || $order->wasChanged('discount_amount')
            || $order->wasChanged('gst_amount')
            || $order->wasChanged('service_charge_amount')
            || $order->wasChanged('total_amount')
        )) {
            $sale = Sale::firstOrNew([
                'order_number' => $order->order_number,
            ]);

            $sale->fill([
                'delivery_type' => $order->delivery_type,
                'payment_method' => $order->payment_method,
                'subtotal' => $order->subtotal_amount,
                'discount_amount' => $order->discount_amount,
                'gst_amount' => $order->gst_amount,
                'service_charge_amount' => $order->service_charge_amount,
                'total' => $order->total_amount,
            ]);
            $sale->completed_at ??= now();
            $sale->save();
        }

        if ($order->wasChanged('status') && $order->status !== 'completed') {
            Sale::where('order_number', $order->order_number)->delete();
        }
    }

    public function deleted(Order $order): void
    {
        Sale::where('order_number', $order->order_number)->delete();
    }
}
