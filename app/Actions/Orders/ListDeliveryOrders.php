<?php

namespace App\Actions\Orders;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class ListDeliveryOrders
{
    public function handle(): Collection
    {
        return Order::query()
            ->with(['customer', 'items'])
            ->whereIn('delivery_type', ['delivery', 'pickup'])
            ->orderByDesc('created_at')
            ->get();
    }
}
