<?php

namespace App\Actions\Sales;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class GetSalesReport
{
    public function handle(array $filters): array
    {
        $sales = $this->applyFilters(Sale::query(), $filters)
            ->orderByDesc('completed_at')
            ->orderByDesc('created_at')
            ->get();

        return [
            'sales' => $sales,
            'stats' => [
                'count' => $sales->count(),
                'grossTotal' => $sales->sum('subtotal'),
                'discountTotal' => $sales->sum('discount_amount'),
                'total' => $sales->sum('total'),
                'cashTotal' => $sales->where('payment_method', 'cash')->sum('total'),
                'cardTotal' => $sales->where('payment_method', 'card')->sum('total'),
                'transferTotal' => $sales->where('payment_method', 'transfer')->sum('total'),
                'dineInCount' => $sales->where('delivery_type', 'dine_in')->count(),
                'deliveryCount' => $sales->where('delivery_type', 'delivery')->count(),
                'pickupCount' => $sales->where('delivery_type', 'pickup')->count(),
            ],
            'filters' => [
                'from' => $filters['from'] ?? null,
                'to' => $filters['to'] ?? null,
                'payment_method' => $filters['payment_method'] ?? null,
                'delivery_type' => $filters['delivery_type'] ?? null,
            ],
        ];
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['from']) && ! empty($filters['to'])) {
            $query->whereBetween('completed_at', [
                Carbon::parse($filters['from'])->startOfDay(),
                Carbon::parse($filters['to'])->endOfDay(),
            ]);
        } else {
            $query->whereDate('completed_at', today());
        }

        if (! empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (! empty($filters['delivery_type'])) {
            $query->where('delivery_type', $filters['delivery_type']);
        }

        return $query;
    }
}
