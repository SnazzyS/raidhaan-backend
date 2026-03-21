<?php

namespace App\Actions\Orders;

use App\Models\Order;

class GenerateOrderReceipt
{
    public function handle(Order $order): Order
    {
        if (in_array($order->status, ['cancelled', 'voided'], true)) {
            abort(422, 'Cancelled or voided tickets cannot be printed.');
        }

        if (blank($order->bill_number)) {
            $order->bill_number = (new BillNumberGenerator())->execute();
        }

        if (blank($order->bill_printed_at)) {
            $order->bill_printed_at = now();
        }

        if ($this->isTableBill($order) && $order->status === 'pending') {
            $order->status = 'printed';
        }

        $order->save();

        return $order->refresh()->load(['customer', 'items']);
    }

    private function isTableBill(Order $order): bool
    {
        return $order->delivery_type === 'dine_in' && filled($order->table_name);
    }
}
