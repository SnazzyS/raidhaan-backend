<?php

namespace App\Actions\Items;

use App\Models\Item;

class DeleteItem
{
    public function handle(Item $item): void
    {
        $item->delete();
    }
}
