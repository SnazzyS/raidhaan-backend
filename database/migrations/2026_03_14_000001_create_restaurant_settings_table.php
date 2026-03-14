<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('restaurant_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('gst_percentage', 5, 2)->default(0);
            $table->boolean('gst_is_inclusive')->default(false);
            $table->decimal('service_charge_percentage', 5, 2)->default(0);
            $table->boolean('service_charge_is_inclusive')->default(false);
            $table->timestamps();git 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_settings');
    }
};
