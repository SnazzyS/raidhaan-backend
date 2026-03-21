<?php

namespace App\Actions\Orders;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListOrders
{
    public function handle(bool $hasStatusFilter = false): Collection
    {
        return QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
            ])
            ->with(['customer', 'items'])
            ->when(
                ! $hasStatusFilter,
                fn ($query) => $query->whereIn('status', ['pending', 'printed'])
            )
            ->orderByDesc('created_at')
            ->get();
    }
}
