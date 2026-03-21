<?php

namespace App\Actions\Categories;

use App\Models\Category;

class UpdateCategory
{
    public function handle(Category $category, array $attributes): Category
    {
        $category->update($attributes);

        return $category->fresh();
    }
}
