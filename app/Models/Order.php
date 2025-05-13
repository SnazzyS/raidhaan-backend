<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'status',
        'delivery_type',
        'payment_method',
        'transfer_reference_number',
        'order_number'
    ];

    protected static function booted()
    {
        static::updating(function ($order) {
            if ($order->isDirty('status') && $order->status === 'completed') {
                Sale::create([
                    'order_number' => $order->order_number,
                    'payment_method' => $order->payment_method,
                    'total' => $order->total_amount
                ]);
            }
        });
    
    }

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
