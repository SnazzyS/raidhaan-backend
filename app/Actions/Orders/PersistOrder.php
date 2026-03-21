<?php

namespace App\Actions\Orders;

use App\Actions\Settings\ResolveRestaurantSettings;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PersistOrder
{
    public function __construct(
        private ResolveRestaurantSettings $resolveRestaurantSettings,
    ) {}

    public function handle(array $validatedData, ?Order $order = null): Order
    {
        $order ??= new Order();

        if ($order->exists && $this->isTableBill($order) && $order->bill_printed_at) {
            throw ValidationException::withMessages([
                'order' => 'Printed table bills cannot be edited. Void the bill instead if it should be removed.',
            ]);
        }

        $orderData = $this->normalizeOrderData($validatedData);

        $this->ensureTableAvailability(
            $orderData['delivery_type'],
            $orderData['table_name'],
            $order->exists ? $order : null,
        );

        return DB::transaction(function () use ($order, $validatedData, $orderData) {
            $settings = $this->resolveRestaurantSettings->handle()['settings'];

            $itemsById = Item::query()
                ->whereIn('id', collect($orderData['items'])->pluck('item_id')->all())
                ->get()
                ->keyBy('id');

            $totals = $this->calculateTotals(
                $itemsById,
                $orderData['items'],
                $orderData['discount_type'],
                $orderData['discount_value'],
                (float) $settings->gst_percentage,
                (bool) $settings->gst_is_inclusive,
                (float) $settings->service_charge_percentage,
                (bool) $settings->service_charge_is_inclusive,
            );

            $customer = $this->resolveCustomer($validatedData, $orderData['delivery_type']);

            if (! $order->exists) {
                $order->order_number = (new OrderNumberGenerator())->execute();
            }

            $order->fill([
                'customer_id' => $customer->id,
                'status' => $orderData['status'],
                'delivery_type' => $orderData['delivery_type'],
                'table_name' => $orderData['delivery_type'] === 'dine_in' ? $orderData['table_name'] : null,
                'payment_method' => $orderData['payment_method'],
                'transfer_reference_number' => $orderData['transfer_reference_number'],
                'subtotal_amount' => $totals['subtotal_amount'],
                'discount_type' => $orderData['discount_type'],
                'discount_value' => $orderData['discount_value'],
                'discount_amount' => $totals['discount_amount'],
                'gst_percentage' => $settings->gst_percentage,
                'gst_amount' => $totals['gst_amount'],
                'gst_is_inclusive' => $settings->gst_is_inclusive,
                'service_charge_percentage' => $settings->service_charge_percentage,
                'service_charge_amount' => $totals['service_charge_amount'],
                'service_charge_is_inclusive' => $settings->service_charge_is_inclusive,
                'total_amount' => $totals['total_amount'],
            ]);

            $order->save();

            $syncPayload = collect($orderData['items'])->mapWithKeys(function (array $selectedItem) use ($itemsById) {
                $item = $itemsById->get($selectedItem['item_id']);

                return [
                    $item->id => [
                        'quantity' => (int) $selectedItem['quantity'],
                        'price' => $item->price,
                    ],
                ];
            })->all();

            $order->items()->sync($syncPayload);

            return $order->fresh(['customer', 'items']);
        });
    }

    private function normalizeOrderData(array $validatedData): array
    {
        $orderData = $validatedData['order'];

        $orderData['table_name'] = $orderData['delivery_type'] === 'dine_in'
            ? trim((string) ($orderData['table_name'] ?? ''))
            : null;
        $orderData['transfer_reference_number'] = $orderData['payment_method'] === 'transfer'
            ? trim((string) ($orderData['transfer_reference_number'] ?? ''))
            : null;
        $orderData['discount_type'] = filled($orderData['discount_type'] ?? null)
            ? $orderData['discount_type']
            : null;
        $orderData['discount_value'] = $orderData['discount_type']
            ? round((float) ($orderData['discount_value'] ?? 0), 2)
            : 0;

        return $orderData;
    }

    private function calculateTotals(
        Collection $itemsById,
        array $selectedItems,
        ?string $discountType,
        float $discountValue,
        float $gstPercentage,
        bool $gstIsInclusive,
        float $serviceChargePercentage,
        bool $serviceChargeIsInclusive,
    ): array {
        $subtotalAmount = round(collect($selectedItems)->sum(function (array $selectedItem) use ($itemsById) {
            $item = $itemsById->get($selectedItem['item_id']);

            return (float) $item->price * (int) $selectedItem['quantity'];
        }), 2);

        $discountAmount = match ($discountType) {
            'percentage' => round($subtotalAmount * ($discountValue / 100), 2),
            'fixed' => round($discountValue, 2),
            default => 0,
        };

        $discountAmount = min($discountAmount, $subtotalAmount);
        $discountedSubtotalAmount = round($subtotalAmount - $discountAmount, 2);

        $inclusiveRate = ($gstIsInclusive ? $gstPercentage : 0) + ($serviceChargeIsInclusive ? $serviceChargePercentage : 0);
        $baseAmount = $inclusiveRate > 0
            ? round($discountedSubtotalAmount / (1 + ($inclusiveRate / 100)), 2)
            : $discountedSubtotalAmount;
        $gstAmount = round($baseAmount * ($gstPercentage / 100), 2);
        $serviceChargeAmount = round($baseAmount * ($serviceChargePercentage / 100), 2);
        $totalAmount = round(
            $discountedSubtotalAmount
            + ($gstIsInclusive ? 0 : $gstAmount)
            + ($serviceChargeIsInclusive ? 0 : $serviceChargeAmount),
            2
        );

        return [
            'subtotal_amount' => $subtotalAmount,
            'discount_amount' => $discountAmount,
            'gst_amount' => $gstAmount,
            'service_charge_amount' => $serviceChargeAmount,
            'total_amount' => $totalAmount,
        ];
    }

    private function resolveCustomer(array $validatedData, string $deliveryType): Customer
    {
        $phoneNumber = trim((string) ($validatedData['phone_number'] ?? ''));
        $address = trim((string) ($validatedData['address'] ?? ''));
        $city = $validatedData['city'] ?? null;

        if ($deliveryType === 'dine_in' && $phoneNumber === '') {
            return Customer::firstOrCreate(
                ['phone_number' => 0],
                ['address' => 'Walk-in guest', 'city' => 'male'],
            );
        }

        return Customer::updateOrCreate(
            ['phone_number' => $phoneNumber],
            [
                'address' => $address !== '' ? $address : 'Walk-in guest',
                'city' => $city ?: 'male',
            ]
        );
    }

    private function ensureTableAvailability(string $deliveryType, ?string $tableName, ?Order $existingOrder = null): void
    {
        if ($deliveryType !== 'dine_in') {
            return;
        }

        $activeOrder = Order::query()
            ->where('delivery_type', 'dine_in')
            ->where('table_name', $tableName)
            ->whereIn('status', ['pending', 'printed'])
            ->when($existingOrder, fn ($query) => $query->whereKeyNot($existingOrder->id))
            ->first();

        if ($activeOrder) {
            throw ValidationException::withMessages([
                'order.table_name' => "{$tableName} already has an active bill.",
            ]);
        }
    }

    private function isTableBill(Order $order): bool
    {
        return $order->delivery_type === 'dine_in' && filled($order->table_name);
    }
}
