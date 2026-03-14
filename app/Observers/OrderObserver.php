<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->status === 'completed' && (
            $order->wasChanged('status')
            || $order->wasChanged('payment_method')
            || $order->wasChanged('total_amount')
        )) {
            Sale::updateOrCreate([
                'order_number' => $order->order_number,
            ], [
                'payment_method' => $order->payment_method,
                'total' => $order->total_amount,
            ]);
        }

        if ($order->wasChanged('status') && $order->status !== 'completed') {
            Sale::where('order_number', $order->order_number)->delete();
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
