<?php

namespace App\Actions\Orders;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ListCancelledOrders
{
    public function handle(?string $from = null, ?string $to = null): Collection
    {
        $query = Order::query()->whereIn('status', ['cancelled', 'voided']);

        if ($from && $to) {
            $query->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        } else {
            $query->whereDate('created_at', today());
        }

        return $query->get();
    }
}
