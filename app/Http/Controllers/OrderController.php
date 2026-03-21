<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrder;
use App\Actions\Orders\GenerateOrderReceipt;
use App\Actions\Orders\GetOrderFormData;
use App\Actions\Orders\ListCancelledOrders;
use App\Actions\Orders\ListDeliveryOrders;
use App\Actions\Orders\ListOrders;
use App\Actions\Orders\ShowOrder;
use App\Actions\Orders\UpdateOrder;
use App\Actions\Orders\UpdateOrderStatus;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request, ListOrders $listOrders)
    {
        return response()->json($listOrders->handle($request->filled('filter.status')));
    }

    public function store(OrderRequest $request, CreateOrder $createOrder)
    {
        $order = $createOrder->handle($request->validated());

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
        ], 201);
    }

    public function show(Order $order, ShowOrder $showOrder)
    {
        return response()->json($showOrder->handle($order));
    }

    public function update(OrderRequest $request, Order $order, UpdateOrder $updateOrder)
    {
        $order = $updateOrder->handle($order, $request->validated());

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order,
        ]);
    }

    public function generateReceipt(Order $order, GenerateOrderReceipt $generateOrderReceipt)
    {
        $order = $generateOrderReceipt->handle($order);

        return response()
            ->view('orders.receipt', [
                'order' => $order,
            ])
            ->header('Content-Type', 'text/html');
    }

    public function cancelledOrders(Request $request, ListCancelledOrders $listCancelledOrders)
    {
        return response()->json($listCancelledOrders->handle(
            $request->input('from'),
            $request->input('to'),
        ));
    }

    public function webIndex(ListDeliveryOrders $listDeliveryOrders)
    {
        return Inertia::render('Orders/Index', [
            'orders' => $listDeliveryOrders->handle(),
        ]);
    }

    public function create(Request $request, GetOrderFormData $getOrderFormData)
    {
        $result = $getOrderFormData->handle(defaults: [
            'delivery_type' => $request->query('delivery_type', $request->filled('table_name') ? 'dine_in' : 'delivery'),
            'table_name' => $request->query('table_name', ''),
            'locked_service_type' => $request->query('delivery_type') === 'dine_in' && $request->filled('table_name'),
        ]);

        return Inertia::render('Orders/Create', $result['data']);
    }

    public function webStore(OrderRequest $request, CreateOrder $createOrder)
    {
        $order = $createOrder->handle($request->validated());

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $this->isTableBill($order) ? 'Table bill opened successfully' : 'Delivery created successfully');
    }

    public function webShow(Order $order, ShowOrder $showOrder)
    {
        return Inertia::render('Orders/Show', [
            'order' => $showOrder->handle($order),
        ]);
    }

    public function edit(Order $order, GetOrderFormData $getOrderFormData)
    {
        $result = $getOrderFormData->handle($order);

        if (! $result['ok']) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', $result['message']);
        }

        return Inertia::render('Orders/Edit', $result['data']);
    }

    public function webUpdate(OrderRequest $request, Order $order, UpdateOrder $updateOrder)
    {
        $order = $updateOrder->handle($order, $request->validated());

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $this->isTableBill($order) ? 'Table bill updated successfully' : 'Delivery updated successfully');
    }

    public function updateStatus(Order $order, OrderStatusRequest $request, UpdateOrderStatus $updateOrderStatus)
    {
        $updateOrderStatus->handle($order, $request->validated('status'));

        return redirect()->back()->with('success', 'Ticket status updated successfully');
    }

    private function isTableBill(Order $order): bool
    {
        return $order->delivery_type === 'dine_in' && filled($order->table_name);
    }
}
