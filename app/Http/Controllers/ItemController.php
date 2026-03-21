<?php

namespace App\Http\Controllers;

use App\Actions\Items\CreateItem;
use App\Actions\Items\DeleteItem;
use App\Actions\Items\GetItemsPageData;
use App\Actions\Items\UpdateItem;
use App\Http\Requests\ItemRequest;
use App\Models\Item;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function store(ItemRequest $request, CreateItem $createItem)
    {
        $createItem->handle($request->validated());

        return response()->json(['message' => 'Item created'], 201);
    }

    public function update(ItemRequest $request, Item $item, UpdateItem $updateItem)
    {
        $updateItem->handle($item, $request->validated());

        return response()->json(['message' => 'Item updated successfully']);
    }

    public function destroy(Item $item, DeleteItem $deleteItem)
    {
        $deleteItem->handle($item);

        return response()->json(['message' => 'Item deleted successfully']);
    }

    public function webIndex(GetItemsPageData $getItemsPageData)
    {
        return Inertia::render('Items/Index', $getItemsPageData->handle());
    }

    public function webStore(ItemRequest $request, CreateItem $createItem)
    {
        $createItem->handle($request->validated());

        return redirect()->back()->with('success', 'Item created successfully');
    }

    public function webUpdate(ItemRequest $request, Item $item, UpdateItem $updateItem)
    {
        $updateItem->handle($item, $request->validated());

        return redirect()->back()->with('success', 'Item updated successfully');
    }

    public function webDestroy(Item $item, DeleteItem $deleteItem)
    {
        $deleteItem->handle($item);

        return redirect()->back()->with('success', 'Item deleted successfully');
    }
}
