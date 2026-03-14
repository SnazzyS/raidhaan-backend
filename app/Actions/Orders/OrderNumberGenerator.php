<?php

namespace App\Actions\Orders;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderNumberGenerator
{
    public function execute(): string
    {
        $dateSegment = now()->format('Ymd');

        $latestOrderNumber = Order::query()
            ->where('order_number', 'like', "ORD-{$dateSegment}-%")
            ->orderByDesc('order_number')
            ->value('order_number');

        $nextSequence = $latestOrderNumber
            ? ((int) Str::afterLast($latestOrderNumber, '-')) + 1
            : 1;

        return sprintf('ORD-%s-%04d', $dateSegment, $nextSequence);
    }
}
