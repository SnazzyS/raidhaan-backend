<?php

namespace App\Actions\Categories;

use App\Models\Category;

class CreateCategory
{
    public function handle(array $attributes): Category
    {
        return Category::create($attributes);
    }
}
