<?php

namespace App\Http\Controllers;

use App\Actions\Orders\OrderNumberGenerator;
use App\Models\Item;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class OrderController extends Controller
{
    // API Methods
    public function index(Request $request)
    {
        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
            ])
            ->with(['customer', 'items'])
            ->where('status', 'pending')
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

        return response()
            ->view('orders.receipt', [
                'order' => $order,
            ])
            ->header('Content-Type', 'text/html');
    }

    public function cancelledOrders(Request $request)
    {
        $query = Order::query()->where('status', 'cancelled');

        if ($request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        } else {
            $query->whereDate('created_at', today());
        }

        $cancelledOrders = $query->get();

        return response()->json($cancelledOrders);
    }

    // Web/Inertia Methods
    public function webIndex(Request $request)
    {
        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
            ])
            ->with(['customer', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function create()
    {
        return Inertia::render('Orders/Create', [
            'items' => Item::with('category')->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function webStore(Request $request)
    {
        $validatedData = $request->validate([
            'phone_number' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'order.status' => 'required|string',
            'order.delivery_type' => 'required|string',
            'order.payment_method' => 'required|string',
            'order.transfer_reference_number' => 'nullable|string',
            'order.items' => 'required|array|min:1',
            'order.items.*.item_id' => 'required|exists:items,id',
            'order.items.*.quantity' => 'required|integer|min:1',
        ]);

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
            $totalAmount += $itemModel->price * $item['quantity'];
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

        return redirect()->route('orders.show', $order)->with('success', 'Order created successfully');
    }

    public function webShow(Order $order)
    {
        $order->load(['customer', 'items']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    public function edit(Order $order)
    {
        $order->load(['customer', 'items']);

        return Inertia::render('Orders/Edit', [
            'order' => $order,
            'items' => Item::with('category')->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function webUpdate(Order $order, Request $request)
    {
        $validatedData = $request->validate([
            'phone_number' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'order.status' => 'required|string',
            'order.delivery_type' => 'required|string',
            'order.payment_method' => 'required|string',
            'order.transfer_reference_number' => 'nullable|string',
            'order.items' => 'required|array|min:1',
            'order.items.*.item_id' => 'required|exists:items,id',
            'order.items.*.quantity' => 'required|integer|min:1',
        ]);

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
            $totalAmount += $itemModel->price * $item['quantity'];
        }

        $order->customer_id = $customer->id;
        $order->status = $validatedData['order']['status'];
        $order->delivery_type = $validatedData['order']['delivery_type'];
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

        return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully');
    }

    public function updateStatus(Order $order, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Order status updated successfully');
    }
}
