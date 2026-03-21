<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class ListCategories
{
    public function handle(bool $withItems = false, bool $withCount = false): Collection
    {
        $query = Category::query()->orderBy('name');

        if ($withItems) {
            $query->with('items');
        }

        if ($withCount) {
            $query->withCount('items');
        }

        return $query->get();
    }
}
