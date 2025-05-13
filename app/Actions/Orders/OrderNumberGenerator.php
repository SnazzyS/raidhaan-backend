<?php

namespace App\Actions\Orders;

use App\Models\Order;

class OrderNumberGenerator
{

    public function execute()
    {
        $totalOrders = Order::count();

        $orderCount = $totalOrders + 1;

        return 'ORD-' . $orderCount;
    }

}
