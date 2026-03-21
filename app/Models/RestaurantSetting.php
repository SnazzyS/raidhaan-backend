<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantSetting extends Model
{
    protected $fillable = [
        'gst_percentage',
        'gst_is_inclusive',
        'service_charge_percentage',
        'service_charge_is_inclusive',
        'table_names',
    ];

    protected $casts = [
        'gst_percentage' => 'decimal:2',
        'gst_is_inclusive' => 'boolean',
        'service_charge_percentage' => 'decimal:2',
        'service_charge_is_inclusive' => 'boolean',
        'table_names' => 'array',
    ];
}
