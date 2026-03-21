<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\RestaurantSetting;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $tableNames = collect(RestaurantSetting::current()->resolvedTableNames());

        $activeTableOrders = Order::with('items')
            ->where('delivery_type', 'dine_in')
            ->whereIn('status', ['pending', 'printed'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('table_name')
            ->keyBy('table_name');

        $tables = $tableNames->map(function (string $tableName) use ($activeTableOrders) {
            $order = $activeTableOrders->get($tableName);

            return [
                'name' => $tableName,
                'status' => $order?->status ?? 'available',
                'href' => $order
                    ? route('orders.show', $order)
                    : route('orders.create', [
                        'delivery_type' => 'dine_in',
                        'table_name' => $tableName,
                    ]),
                'order_id' => $order?->id,
                'order_number' => $order?->order_number,
                'bill_number' => $order?->bill_number,
                'item_count' => $order?->items->count() ?? 0,
                'total_amount' => $order?->total_amount ?? 0,
            ];
        })->values();

        $stats = [
            'tableCount' => $tables->count(),
            'occupiedCount' => $tables->where('status', '!=', 'available')->count(),
            'availableCount' => $tables->where('status', 'available')->count(),
            'printedCount' => $tables->where('status', 'printed')->count(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'tables' => $tables,
        ]);
    }
}
