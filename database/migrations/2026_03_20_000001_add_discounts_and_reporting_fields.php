<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('subtotal_amount');
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_value');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('delivery_type')->nullable()->after('order_number');
            $table->decimal('subtotal', 10, 2)->default(0)->after('payment_method');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('gst_amount', 10, 2)->default(0)->after('discount_amount');
            $table->decimal('service_charge_amount', 10, 2)->default(0)->after('gst_amount');
            $table->timestamp('completed_at')->nullable()->after('total');
            $table->index(['completed_at', 'payment_method']);
            $table->index(['completed_at', 'delivery_type']);
        });

        DB::table('sales')->orderBy('id')->get()->each(function (object $sale) {
            $order = DB::table('orders')
                ->where('order_number', $sale->order_number)
                ->first();

            DB::table('sales')
                ->where('id', $sale->id)
                ->update([
                    'delivery_type' => $order->delivery_type ?? null,
                    'subtotal' => $order->subtotal_amount ?? $sale->total,
                    'discount_amount' => $order->discount_amount ?? 0,
                    'gst_amount' => $order->gst_amount ?? 0,
                    'service_charge_amount' => $order->service_charge_amount ?? 0,
                    'completed_at' => $sale->created_at,
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['completed_at', 'payment_method']);
            $table->dropIndex(['completed_at', 'delivery_type']);
            $table->dropColumn([
                'delivery_type',
                'subtotal',
                'discount_amount',
                'gst_amount',
                'service_charge_amount',
                'completed_at',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'discount_amount',
            ]);
        });
    }
};
