<?php

namespace App\Actions\Orders;

use App\Models\Order;

class CreateOrder
{
    public function __construct(
        private PersistOrder $persistOrder,
    ) {}

    public function handle(array $validatedData): Order
    {
        return $this->persistOrder->handle($validatedData);
    }
}
