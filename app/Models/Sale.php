<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'order_number',
        'delivery_type',
        'payment_method',
        'subtotal',
        'discount_amount',
        'gst_amount',
        'service_charge_amount',
        'total',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'service_charge_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'completed_at' => 'datetime',
    ];
}
