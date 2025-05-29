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
use Illuminate\Support\Carbon;

class OrderController extends Controller
{

    // public function index(Request $request)
    // {

    //     $orders = QueryBuilder::for(Order::class)
    //     ->allowedFilters([
    //     AllowedFilter::exact('status'),
    //     ])
    //     ->with(['customer', 'items'])
    //     ->where('status', 'pending')
    //     ->get();



    //     return response()->json($orders);

    // }

    public function index(Request $request)
    {
        $query = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('status'), // This allows ?status=cancelled
                // AllowedFilter::scope('date'), // If you have a date scope defined in your Order model
                                             // Alternatively, handle date filtering manually as below
            ])
            ->with(['customer', 'items']);

        // Handle date filtering for 'today'
        if ($request->query('date') === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }
        
        // If a specific status is requested (like 'cancelled'),
        // QueryBuilder's AllowedFilter::exact('status') should handle it.
        // If 'date=today' is present AND no specific status is requested by the HistoryView's "All Today's" button,
        // you might want a default set of statuses for that specific view.
        // However, for the "Cancelled" view, it explicitly sends status=cancelled.

        // Example: If the HistoryView's "All Today's" needs a specific set of statuses
        // if ($request->query('date') === 'today' && !$request->has('status') && $request->query('source') === 'history_all') {
        //     $query->whereIn('status', ['pending', 'completed', 'cancelled']);
        // }


        $orders = $query->orderBy('created_at', 'desc')->get();

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

        return response()
            ->view('orders.receipt', ['order' => $order])
            ->header('Content-Type', 'text/html');
    }
}
