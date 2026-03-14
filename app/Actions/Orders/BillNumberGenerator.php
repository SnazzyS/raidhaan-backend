<?php

namespace App\Actions\Orders;

use App\Models\Order;
use Illuminate\Support\Str;

class BillNumberGenerator
{
    public function execute(): string
    {
        $dateSegment = now()->format('Ymd');

        $latestBillNumber = Order::query()
            ->whereNotNull('bill_number')
            ->where('bill_number', 'like', "BILL-{$dateSegment}-%")
            ->orderByDesc('bill_number')
            ->value('bill_number');

        $nextSequence = $latestBillNumber
            ? ((int) Str::afterLast($latestBillNumber, '-')) + 1
            : 1;

        return sprintf('BILL-%s-%04d', $dateSegment, $nextSequence);
    }
}
