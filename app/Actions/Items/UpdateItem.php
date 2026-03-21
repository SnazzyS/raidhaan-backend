<?php

namespace App\Actions\Items;

use App\Models\Item;

class UpdateItem
{
    public function handle(Item $item, array $attributes): Item
    {
        $item->update($attributes);

        return $item->fresh('category');
    }
}
