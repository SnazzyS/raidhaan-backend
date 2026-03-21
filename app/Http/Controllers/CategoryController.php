<?php

namespace App\Http\Controllers;

use App\Actions\Categories\CreateCategory;
use App\Actions\Categories\DeleteCategory;
use App\Actions\Categories\ListCategories;
use App\Actions\Categories\UpdateCategory;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(ListCategories $listCategories)
    {
        return response()->json($listCategories->handle(withItems: true));
    }

    public function store(CategoryRequest $request, CreateCategory $createCategory)
    {
        $createCategory->handle($request->validated());

        return response()->json([
            'message' => 'Category created',
        ], 201);
    }

    public function update(CategoryRequest $request, Category $category, UpdateCategory $updateCategory)
    {
        $updateCategory->handle($category, $request->validated());

        return response()->json([
            'message' => 'Category updated successfully',
        ], 200);
    }

    public function destroy(Category $category, DeleteCategory $deleteCategory)
    {
        $deleteCategory->handle($category);

        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function webIndex(ListCategories $listCategories)
    {
        return Inertia::render('Categories/Index', [
            'categories' => $listCategories->handle(withCount: true),
        ]);
    }

    public function webStore(CategoryRequest $request, CreateCategory $createCategory)
    {
        $createCategory->handle($request->validated());

        return redirect()->back()->with('success', 'Category created successfully');
    }

    public function webUpdate(CategoryRequest $request, Category $category, UpdateCategory $updateCategory)
    {
        $updateCategory->handle($category, $request->validated());

        return redirect()->back()->with('success', 'Category updated successfully');
    }

    public function webDestroy(Category $category, DeleteCategory $deleteCategory)
    {
        $deleteCategory->handle($category);

        return redirect()->back()->with('success', 'Category deleted successfully');
    }
}
