<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;

class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        $validatedData = $request->validated();
        
        $customer = Customer::updateOrCreate(
            ['phone_number' => $validatedData['phone_number']],
            [
                'address' => $validatedData['address'],
                'city' => $validatedData['city'],
            ]
        );

        $totalAmount = 0;

        foreach ($validatedData['order']['items'] as $item) {
            $itemModel = Item::find($item['item_id']);
            $totalAmount += $itemModel['price'] * $item['quantity'];
        }

        $order = new Order();
        $order->customer_id = $customer->id;
        $order->status = $validatedData['order']['status'];
        $order->delivery_type = $validatedData['order']['delivery_type'];
        $order->payment_method = $validatedData['order']['payment_method'];

        if (isset($validatedData['order']['transfer_reference_number'])) {
            $order->transfer_reference_number = $validatedData['order']['transfer_reference_number'];
        }

        
        $order->total_amount = $totalAmount;
        $order->save();


        foreach ($validatedData['order']['items'] as $item) {
            $itemModel = Item::findOrFail($item['item_id']);
            $order->items()->attach($itemModel->id, [
                'quantity' => $item['quantity'],
                'price' => $itemModel->price,
            ]);
        }
        
        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
        ], 201);
    }
}
