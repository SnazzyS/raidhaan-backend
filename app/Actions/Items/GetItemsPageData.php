<?php

namespace App\Actions\Items;

use App\Models\Category;
use App\Models\Item;

class GetItemsPageData
{
    public function handle(): array
    {
        return [
            'items' => Item::with('category')
                ->orderBy('name')
                ->get(),
            'categories' => Category::orderBy('name')->get(),
        ];
    }
}
