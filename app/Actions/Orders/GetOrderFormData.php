<?php

namespace App\Actions\Orders;

use App\Actions\Settings\ResolveRestaurantSettings;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;

class GetOrderFormData
{
    public function __construct(
        private ResolveRestaurantSettings $resolveRestaurantSettings,
    ) {}

    public function handle(?Order $order = null, array $defaults = []): array
    {
        if ($order && $this->isTableBill($order) && $order->bill_printed_at) {
            return [
                'ok' => false,
                'message' => 'Printed table bills cannot be edited. Void the bill instead if it should be removed.',
            ];
        }

        $resolvedSettings = $this->resolveRestaurantSettings->handle();

        return [
            'ok' => true,
            'data' => [
                'order' => $order?->load(['customer', 'items']),
                'items' => Item::with('category')->orderBy('name')->get(),
                'categories' => Category::orderBy('name')->get(),
                'tables' => collect($resolvedSettings['table_names'])
                    ->map(fn (string $tableName) => [
                        'value' => $tableName,
                        'label' => $tableName,
                    ])
                    ->values()
                    ->all(),
                'settings' => $resolvedSettings['settings'],
                'defaults' => [
                    'delivery_type' => $defaults['delivery_type'] ?? 'delivery',
                    'table_name' => $defaults['table_name'] ?? '',
                    'locked_service_type' => $defaults['locked_service_type'] ?? false,
                ],
            ],
        ];
    }

    private function isTableBill(Order $order): bool
    {
        return $order->delivery_type === 'dine_in' && filled($order->table_name);
    }
}
