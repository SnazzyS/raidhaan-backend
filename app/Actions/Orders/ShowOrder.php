<?php

namespace App\Actions\Orders;

use App\Models\Order;

class ShowOrder
{
    public function handle(Order $order): Order
    {
        return $order->load(['customer', 'items']);
    }
}
