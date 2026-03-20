<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DiscountedSalesReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_discounted_order_totals_flow_into_completed_sales(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);
        $category = Category::create([
            'name' => 'Main',
        ]);
        $item = Item::create([
            'name' => 'Burger',
            'price' => 20,
            'category_id' => $category->id,
        ]);

        $createResponse = $this->actingAs($staff)->post('/orders', [
            'phone_number' => '7123456',
            'address' => 'Main Street',
            'city' => 'male',
            'order' => [
                'status' => 'pending',
                'delivery_type' => 'delivery',
                'payment_method' => 'cash',
                'transfer_reference_number' => '',
                'discount_type' => 'fixed',
                'discount_value' => 10,
                'items' => [
                    [
                        'item_id' => $item->id,
                        'quantity' => 2,
                    ],
                ],
            ],
        ]);

        $createResponse->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();

        $this->assertSame('fixed', $order->discount_type);
        $this->assertSame('10.00', $order->discount_value);
        $this->assertSame('10.00', $order->discount_amount);
        $this->assertSame('40.00', $order->subtotal_amount);
        $this->assertSame('30.00', $order->total_amount);

        $completeResponse = $this->actingAs($staff)->patch("/orders/{$order->id}/status", [
            'status' => 'completed',
        ]);

        $completeResponse->assertRedirect();

        $this->assertDatabaseHas('sales', [
            'order_number' => $order->order_number,
            'delivery_type' => 'delivery',
            'payment_method' => 'cash',
            'subtotal' => 40,
            'discount_amount' => 10,
            'total' => 30,
        ]);
    }

    public function test_sales_reporting_filters_and_stats_include_discounts(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Sale::create([
            'order_number' => 'ORD-20260320-001',
            'delivery_type' => 'dine_in',
            'payment_method' => 'card',
            'subtotal' => 50,
            'discount_amount' => 5,
            'gst_amount' => 0,
            'service_charge_amount' => 0,
            'total' => 45,
            'completed_at' => '2026-03-20 12:00:00',
        ]);

        Sale::create([
            'order_number' => 'ORD-20260320-002',
            'delivery_type' => 'delivery',
            'payment_method' => 'cash',
            'subtotal' => 60,
            'discount_amount' => 0,
            'gst_amount' => 0,
            'service_charge_amount' => 0,
            'total' => 60,
            'completed_at' => '2026-03-20 13:00:00',
        ]);

        Sale::create([
            'order_number' => 'ORD-20260319-003',
            'delivery_type' => 'dine_in',
            'payment_method' => 'card',
            'subtotal' => 40,
            'discount_amount' => 2,
            'gst_amount' => 0,
            'service_charge_amount' => 0,
            'total' => 38,
            'completed_at' => '2026-03-19 11:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/sales?from=2026-03-20&to=2026-03-20&payment_method=card&delivery_type=dine_in');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Sales/Index')
            ->where('filters.from', '2026-03-20')
            ->where('filters.to', '2026-03-20')
            ->where('filters.payment_method', 'card')
            ->where('filters.delivery_type', 'dine_in')
            ->has('sales', 1)
            ->where('sales.0.order_number', 'ORD-20260320-001')
            ->where('stats.count', 1)
            ->where('stats.grossTotal', 50)
            ->where('stats.discountTotal', 5)
            ->where('stats.total', 45)
            ->where('stats.cardTotal', 45)
            ->where('stats.dineInCount', 1)
        );
    }
}
