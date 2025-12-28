<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ItemRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    // API Methods
    public function store(ItemRequest $request)
    {
        Item::create($request->validated());

        return response()->json(['message' => 'Item created'], 201);
    }

    public function update(Item $item, ItemRequest $request)
    {
        $item->update($request->validated());

        return response()->json(['message' => 'Item updated successfully']);
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }

    // Web/Inertia Methods
    public function webIndex()
    {
        return Inertia::render('Items/Index', [
            'items' => Item::with('category')
                ->orderBy('name')
                ->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function webStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        Item::create($validated);

        return redirect()->back()->with('success', 'Item created successfully');
    }

    public function webUpdate(Item $item, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $item->update($validated);

        return redirect()->back()->with('success', 'Item updated successfully');
    }

    public function webDestroy(Item $item)
    {
        $item->delete();

        return redirect()->back()->with('success', 'Item deleted successfully');
    }
}
