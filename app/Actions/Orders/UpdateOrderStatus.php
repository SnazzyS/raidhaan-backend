<?php

namespace App\Actions\Orders;

use App\Models\Order;
use Illuminate\Validation\ValidationException;

class UpdateOrderStatus
{
    public function handle(Order $order, string $newStatus): void
    {
        $this->ensureStatusTransitionIsAllowed($order, $newStatus);

        $order->update(['status' => $newStatus]);
    }

    private function ensureStatusTransitionIsAllowed(Order $order, string $newStatus): void
    {
        if ($order->status === $newStatus) {
            return;
        }

        if ($this->isTableBill($order)) {
            if ($order->bill_printed_at) {
                if (! in_array($newStatus, ['completed', 'voided'], true)) {
                    throw ValidationException::withMessages([
                        'status' => 'Printed table bills can only be completed or voided.',
                    ]);
                }

                return;
            }

            if (! in_array($newStatus, ['pending', 'cancelled'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Table bills must be printed before they can be completed.',
                ]);
            }

            return;
        }

        if (! in_array($newStatus, ['pending', 'completed', 'cancelled'], true)) {
            throw ValidationException::withMessages([
                'status' => 'Deliveries can only move between pending, completed, and cancelled.',
            ]);
        }
    }

    private function isTableBill(Order $order): bool
    {
        return $order->delivery_type === 'dine_in' && filled($order->table_name);
    }
}
