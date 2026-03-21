<?php

namespace App\Actions\Orders;

use App\Models\Order;

class UpdateOrder
{
    public function __construct(
        private PersistOrder $persistOrder,
    ) {}

    public function handle(Order $order, array $validatedData): Order
    {
        return $this->persistOrder->handle($validatedData, $order);
    }
}
