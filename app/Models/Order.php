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
