<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
            $table->string('delivery_type')->default('delivery')->change();
            $table->string('table_name')->nullable()->after('delivery_type');
            $table->string('bill_number')->nullable()->unique()->after('order_number');
            $table->timestamp('bill_printed_at')->nullable()->after('bill_number');
            $table->decimal('subtotal_amount', 10, 2)->default(0)->after('transfer_reference_number');
            $table->decimal('gst_percentage', 5, 2)->default(0)->after('subtotal_amount');
            $table->decimal('gst_amount', 10, 2)->default(0)->after('gst_percentage');
            $table->boolean('gst_is_inclusive')->default(false)->after('gst_amount');
            $table->decimal('service_charge_percentage', 5, 2)->default(0)->after('gst_is_inclusive');
            $table->decimal('service_charge_amount', 10, 2)->default(0)->after('service_charge_percentage');
            $table->boolean('service_charge_is_inclusive')->default(false)->after('service_charge_amount');
            $table->index(['table_name', 'status']);
        });

        DB::table('orders')->update([
            'subtotal_amount' => DB::raw('total_amount'),
        ]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['table_name', 'status']);
            $table->dropUnique(['bill_number']);
            $table->dropColumn([
                'table_name',
                'bill_number',
                'bill_printed_at',
                'subtotal_amount',
                'gst_percentage',
                'gst_amount',
                'gst_is_inclusive',
                'service_charge_percentage',
                'service_charge_amount',
                'service_charge_is_inclusive',
            ]);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending')->change();
            $table->enum('delivery_type', ['pickup', 'delivery'])->change();
        });
    }
};
