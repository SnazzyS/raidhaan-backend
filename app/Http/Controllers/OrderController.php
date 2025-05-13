<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;

class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        $validatedData = $request->validated();
        dd($validatedData);

        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $orderItems[] = [
                'item_id' => $item['id'],
                'quantity' => $item['quantity'],
            ];

            // Assuming you have a method to get the price of an item
            $totalAmount += $this->getItemPrice($item['id']) * $item['quantity'];
        }



        return response()->json();
    }
}
