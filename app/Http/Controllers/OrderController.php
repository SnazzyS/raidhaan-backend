<?php

namespace App\Http\Controllers;

use App\Actions\Orders\BillNumberGenerator;
use App\Actions\Orders\OrderNumberGenerator;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\RestaurantSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
            ])
            ->with(['customer', 'items'])
            ->when(
                ! $request->filled('filter.status'),
                fn ($query) => $query->whereIn('status', ['pending', 'printed'])
            )
            ->orderByDesc('created_at')
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateOrderPayload($request);
        $order = $this->persistOrder(new Order(), $validatedData);

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

    public function update(Request $request, Order $order)
    {
        $validatedData = $this->validateOrderPayload($request, $order);
        $order = $this->persistOrder($order, $validatedData);

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order,
        ]);
    }

    public function generateReceipt(Order $order)
    {
        if (in_array($order->status, ['cancelled', 'voided'], true)) {
            abort(422, 'Cancelled or voided tickets cannot be printed.');
        }

        $this->markOrderPrinted($order);
        $order->refresh()->load(['customer', 'items']);

        return response()
            ->view('orders.receipt', [
                'order' => $order,
            ])
            ->header('Content-Type', 'text/html');
    }

    public function cancelledOrders(Request $request)
    {
        $query = Order::query()->whereIn('status', ['cancelled', 'voided']);

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

    public function webIndex(Request $request)
    {
        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
            ])
            ->with(['customer', 'items'])
            ->whereIn('delivery_type', ['delivery', 'pickup'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Orders/Create', [
            'items' => Item::with('category')->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'tables' => $this->tableOptions(),
            'settings' => $this->chargeSettings(),
            'defaults' => [
                'delivery_type' => $request->query('delivery_type', $request->filled('table_name') ? 'dine_in' : 'delivery'),
                'table_name' => $request->query('table_name', ''),
                'locked_service_type' => $request->query('delivery_type') === 'dine_in' && $request->filled('table_name'),
            ],
        ]);
    }

    public function webStore(Request $request)
    {
        $validatedData = $this->validateOrderPayload($request);
        $order = $this->persistOrder(new Order(), $validatedData);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $order->isTableBill() ? 'Table bill opened successfully' : 'Delivery created successfully');
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
        if ($order->isTableBill() && $order->bill_printed_at) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Printed table bills cannot be edited. Void the bill instead if it should be removed.');
        }

        $order->load(['customer', 'items']);

        return Inertia::render('Orders/Edit', [
            'order' => $order,
            'items' => Item::with('category')->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'tables' => $this->tableOptions(),
            'settings' => $this->chargeSettings(),
        ]);
    }

    public function webUpdate(Order $order, Request $request)
    {
        $validatedData = $this->validateOrderPayload($request, $order);
        $order = $this->persistOrder($order, $validatedData);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $order->isTableBill() ? 'Table bill updated successfully' : 'Delivery updated successfully');
    }

    public function updateStatus(Order $order, Request $request)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'printed', 'completed', 'cancelled', 'voided'])],
        ]);

        $this->ensureStatusTransitionIsAllowed($order, $validated['status']);

        $order->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Ticket status updated successfully');
    }

    private function validateOrderPayload(Request $request, ?Order $existingOrder = null): array
    {
        if ($existingOrder && $existingOrder->isTableBill() && $existingOrder->bill_printed_at) {
            throw ValidationException::withMessages([
                'order' => 'Printed table bills cannot be edited. Void the bill instead if it should be removed.',
            ]);
        }

        $validated = $request->validate([
            'phone_number' => ['nullable', 'string', 'max:20', 'required_unless:order.delivery_type,dine_in'],
            'address' => ['nullable', 'string', 'max:255', 'required_unless:order.delivery_type,dine_in'],
            'city' => ['nullable', Rule::in(['male', 'hulhumale phase 1', 'hulhumale phase 2']), 'required_unless:order.delivery_type,dine_in'],
            'order.status' => ['required', Rule::in(['pending', 'printed', 'completed', 'cancelled', 'voided'])],
            'order.delivery_type' => ['required', Rule::in(['delivery', 'pickup', 'dine_in'])],
            'order.table_name' => [
                'nullable',
                'string',
                Rule::requiredIf($request->input('order.delivery_type') === 'dine_in'),
                Rule::in($this->tableNames()),
            ],
            'order.payment_method' => ['required', Rule::in(['transfer', 'cash', 'card'])],
            'order.transfer_reference_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf($request->input('order.payment_method') === 'transfer'),
            ],
            'order.items' => ['required', 'array', 'min:1'],
            'order.items.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'order.items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $validated['order']['table_name'] = $validated['order']['delivery_type'] === 'dine_in'
            ? trim((string) ($validated['order']['table_name'] ?? ''))
            : null;
        $validated['order']['transfer_reference_number'] = $validated['order']['payment_method'] === 'transfer'
            ? trim((string) ($validated['order']['transfer_reference_number'] ?? ''))
            : null;

        $this->ensureTableAvailability(
            $validated['order']['delivery_type'],
            $validated['order']['table_name'],
            $existingOrder
        );

        return $validated;
    }

    private function persistOrder(Order $order, array $validatedData): Order
    {
        return DB::transaction(function () use ($order, $validatedData) {
            $orderData = $validatedData['order'];
            $settings = RestaurantSetting::current();

            $itemsById = Item::query()
                ->whereIn('id', collect($orderData['items'])->pluck('item_id')->all())
                ->get()
                ->keyBy('id');

            $totals = $this->calculateTotals(
                $itemsById,
                $orderData['items'],
                (float) $settings->gst_percentage,
                (bool) $settings->gst_is_inclusive,
                (float) $settings->service_charge_percentage,
                (bool) $settings->service_charge_is_inclusive,
            );

            $customer = $this->resolveCustomer($validatedData, $orderData['delivery_type']);

            if (! $order->exists) {
                $order->order_number = (new OrderNumberGenerator())->execute();
            }

            $order->fill([
                'customer_id' => $customer->id,
                'status' => $orderData['status'],
                'delivery_type' => $orderData['delivery_type'],
                'table_name' => $orderData['delivery_type'] === 'dine_in' ? $orderData['table_name'] : null,
                'payment_method' => $orderData['payment_method'],
                'transfer_reference_number' => $orderData['transfer_reference_number'],
                'subtotal_amount' => $totals['subtotal_amount'],
                'gst_percentage' => $settings->gst_percentage,
                'gst_amount' => $totals['gst_amount'],
                'gst_is_inclusive' => $settings->gst_is_inclusive,
                'service_charge_percentage' => $settings->service_charge_percentage,
                'service_charge_amount' => $totals['service_charge_amount'],
                'service_charge_is_inclusive' => $settings->service_charge_is_inclusive,
                'total_amount' => $totals['total_amount'],
            ]);

            $order->save();

            $syncPayload = collect($orderData['items'])->mapWithKeys(function (array $selectedItem) use ($itemsById) {
                $item = $itemsById->get($selectedItem['item_id']);

                return [
                    $item->id => [
                        'quantity' => (int) $selectedItem['quantity'],
                        'price' => $item->price,
                    ],
                ];
            })->all();

            $order->items()->sync($syncPayload);

            return $order->fresh(['customer', 'items']);
        });
    }

    private function calculateTotals(
        Collection $itemsById,
        array $selectedItems,
        float $gstPercentage,
        bool $gstIsInclusive,
        float $serviceChargePercentage,
        bool $serviceChargeIsInclusive,
    ): array {
        $subtotalAmount = round(collect($selectedItems)->sum(function (array $selectedItem) use ($itemsById) {
            $item = $itemsById->get($selectedItem['item_id']);

            return (float) $item->price * (int) $selectedItem['quantity'];
        }), 2);

        $inclusiveRate = ($gstIsInclusive ? $gstPercentage : 0) + ($serviceChargeIsInclusive ? $serviceChargePercentage : 0);
        $baseAmount = $inclusiveRate > 0
            ? round($subtotalAmount / (1 + ($inclusiveRate / 100)), 2)
            : $subtotalAmount;
        $gstAmount = round($baseAmount * ($gstPercentage / 100), 2);
        $serviceChargeAmount = round($baseAmount * ($serviceChargePercentage / 100), 2);
        $totalAmount = round(
            $subtotalAmount
            + ($gstIsInclusive ? 0 : $gstAmount)
            + ($serviceChargeIsInclusive ? 0 : $serviceChargeAmount),
            2
        );

        return [
            'subtotal_amount' => $subtotalAmount,
            'gst_amount' => $gstAmount,
            'service_charge_amount' => $serviceChargeAmount,
            'total_amount' => $totalAmount,
        ];
    }

    private function resolveCustomer(array $validatedData, string $deliveryType): Customer
    {
        $phoneNumber = trim((string) ($validatedData['phone_number'] ?? ''));
        $address = trim((string) ($validatedData['address'] ?? ''));
        $city = $validatedData['city'] ?? null;

        if ($deliveryType === 'dine_in' && $phoneNumber === '') {
            return Customer::firstOrCreate(
                ['phone_number' => 0],
                ['address' => 'Walk-in guest', 'city' => 'male']
            );
        }

        return Customer::updateOrCreate(
            ['phone_number' => $phoneNumber],
            [
                'address' => $address !== '' ? $address : 'Walk-in guest',
                'city' => $city ?: 'male',
            ]
        );
    }

    private function ensureTableAvailability(string $deliveryType, ?string $tableName, ?Order $existingOrder = null): void
    {
        if ($deliveryType !== 'dine_in') {
            return;
        }

        $activeOrder = Order::query()
            ->where('delivery_type', 'dine_in')
            ->where('table_name', $tableName)
            ->whereIn('status', ['pending', 'printed'])
            ->when($existingOrder, fn ($query) => $query->whereKeyNot($existingOrder->id))
            ->first();

        if ($activeOrder) {
            throw ValidationException::withMessages([
                'order.table_name' => "{$tableName} already has an active bill.",
            ]);
        }
    }

    private function ensureStatusTransitionIsAllowed(Order $order, string $newStatus): void
    {
        if ($order->status === $newStatus) {
            return;
        }

        if ($order->isTableBill()) {
            if ($order->bill_printed_at) {
                if (! in_array($newStatus, ['completed', 'voided'], true)) {
                    throw ValidationException::withMessages([
                        'status' => 'Printed table bills can only be completed or voided.',
                    ]);
                }

                return;
            }

            if (! in_array($newStatus, ['pending', 'cancelled'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Table bills must be printed before they can be completed.',
                ]);
            }

            return;
        }

        if (! in_array($newStatus, ['pending', 'completed', 'cancelled'], true)) {
            throw ValidationException::withMessages([
                'status' => 'Deliveries can only move between pending, completed, and cancelled.',
            ]);
        }
    }

    private function markOrderPrinted(Order $order): void
    {
        if (blank($order->bill_number)) {
            $order->bill_number = (new BillNumberGenerator())->execute();
        }

        if (blank($order->bill_printed_at)) {
            $order->bill_printed_at = now();
        }

        if ($order->isTableBill() && $order->status === 'pending') {
            $order->status = 'printed';
        }

        $order->save();
    }

    private function tableNames(): array
    {
        return config('restaurant.tables', []);
    }

    private function tableOptions(): array
    {
        return collect($this->tableNames())
            ->map(fn (string $tableName) => [
                'value' => $tableName,
                'label' => $tableName,
            ])
            ->values()
            ->all();
    }

    private function chargeSettings(): RestaurantSetting
    {
        return RestaurantSetting::current();
    }
}
