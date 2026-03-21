<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'status',
        'delivery_type',
        'table_name',
        'payment_method',
        'transfer_reference_number',
        'order_number',
        'bill_number',
        'bill_printed_at',
        'subtotal_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'gst_percentage',
        'gst_amount',
        'gst_is_inclusive',
        'service_charge_percentage',
        'service_charge_amount',
        'service_charge_is_inclusive',
        'total_amount',
    ];

    protected $casts = [
        'bill_printed_at' => 'datetime',
        'gst_is_inclusive' => 'boolean',
        'service_charge_is_inclusive' => 'boolean',
        'subtotal_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'service_charge_percentage' => 'decimal:2',
        'service_charge_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_order')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
