<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\ItemRequest;

class ItemController extends Controller
{
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
}
