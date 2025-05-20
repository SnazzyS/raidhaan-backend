<?php

namespace App\Http\Controllers;

use App\Actions\Orders\OrderNumberGenerator;
use App\Models\Item;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class OrderController extends Controller
{

    public function index(Request $request)
    {

        // $orders = QueryBuilder::for(Order::class)
        // ->allowedFilters([
        //  AllowedFilter::exact('status'),
        // ])
        // ->with(['customer', 'items'])
        // ->whereDate('created_at', today())
        // ->get();

        $orders = QueryBuilder::for(Order::class)
        ->allowedFilters([
        AllowedFilter::exact('status'),
        ])
        ->with(['customer', 'items'])
        ->where('status', 'pending')  // Filter for pending orders
        ->get();

        return response()->json($orders);

    }

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

        $order->order_number = (new OrderNumberGenerator())->execute();

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

    public function show(Order $order)
    {
        $order->load(['customer', 'items']);
        return response()->json($order);
    }

    public function update(OrderRequest $request, Order $order)
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

        $order->customer_id = $customer->id;
        
        $order->status = $validatedData['order']['status'];
    
        $order->delivery_type = $validatedData['order']['delivery_type'] === 'pickup' ? 'pickup' : $validatedData['order']['delivery_type'];
    
        $order->payment_method = $validatedData['order']['payment_method'];
    
        if (isset($validatedData['order']['transfer_reference_number'])) {
            $order->transfer_reference_number = $validatedData['order']['transfer_reference_number'];
        }
    
        $order->total_amount = $totalAmount;

   


        $order->save();

        $order->items()->detach();
    
        foreach ($validatedData['order']['items'] as $item) {
            $itemModel = Item::findOrFail($item['item_id']);
            $order->items()->attach($itemModel->id, [
                'quantity' => $item['quantity'],
                'price' => $itemModel->price,
            ]);
        }
    
        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order,
        ]);
    }

    public function generateReceipt(Order $order)
    {
        $order->load(['customer', 'items']);

        $lines[] = str_pad("RAIDHAAN CAFE", 30, " ", STR_PAD_BOTH);
        $lines[] = "Order #: " . $order->order_number;
        $lines[] = "------------------------------";
        $lines[] = "Item       Qty   Total";

        foreach ($order->items as $item) {
            $total = number_format($item->pivot->price * $item->pivot->quantity, 2);
            $lines[] = sprintf("%-10s %3d %8s", $item->name, $item->pivot->quantity, $total);
        }

        $lines[] = "------------------------------";
        $lines[] = "Total: MVR " . number_format($order->total_amount, 2);
        $lines[] = "     Thank you for ordering!     ";

        // response
        return response()->json([
         'receipt' => implode("\n", $lines),
    ]);
    }
}
