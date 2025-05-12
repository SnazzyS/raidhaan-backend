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
        $categories = Category::all();

        return response()->json($categories);
    }

    public function store(CategoryRequest $request)
    {

        Category::create($request->validated());
        
        return response()->json([
                'message' => 'Category created'
            ], 201);

    }

    // not needed?
    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        
        return response()->json([
            'message' => 'Category updated successfully'
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }

}
