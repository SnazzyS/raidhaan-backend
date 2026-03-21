<?php

namespace App\Actions\Categories;

use App\Models\Category;

class DeleteCategory
{
    public function handle(Category $category): void
    {
        $category->delete();
    }
}
