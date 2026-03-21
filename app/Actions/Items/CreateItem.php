<?php

namespace App\Actions\Items;

use App\Models\Item;

class CreateItem
{
    public function handle(array $attributes): Item
    {
        return Item::create($attributes);
    }
}
