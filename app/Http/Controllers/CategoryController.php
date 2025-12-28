<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    // API Methods
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

    // Web/Inertia Methods
    public function webIndex()
    {
        return Inertia::render('Categories/Index', [
            'categories' => Category::withCount('items')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function webStore(CategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->back()->with('success', 'Category created successfully');
    }

    public function webUpdate(Category $category, CategoryRequest $request)
    {
        $category->update($request->validated());

        return redirect()->back()->with('success', 'Category updated successfully');
    }

    public function webDestroy(Category $category)
    {
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully');
    }
}
