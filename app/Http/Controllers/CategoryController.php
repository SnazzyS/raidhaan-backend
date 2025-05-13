<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use PDO;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::with('items')->get());
    }

    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());
        
        return response()->json([
                'message' => 'Category created'
            ], 201);

    }


    public function update(Category $category, CategoryRequest $request)
    {
        $category->update($request->validated());
        
        return response()->json([
            'message' => 'Category updated successfully'
        ], 200);
    }

   
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
