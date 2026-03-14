<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class RestaurantSetting extends Model
{
    protected $fillable = [
        'gst_percentage',
        'gst_is_inclusive',
        'service_charge_percentage',
        'service_charge_is_inclusive',
    ];

    protected $casts = [
        'gst_percentage' => 'decimal:2',
        'gst_is_inclusive' => 'boolean',
        'service_charge_percentage' => 'decimal:2',
        'service_charge_is_inclusive' => 'boolean',
    ];

    public static function current(): self
    {
        if (! Schema::hasTable('restaurant_settings')) {
            return new static([
                'gst_percentage' => config('restaurant.charges.gst_percentage', 0),
                'gst_is_inclusive' => config('restaurant.charges.gst_is_inclusive', false),
                'service_charge_percentage' => config('restaurant.charges.service_charge_percentage', 0),
                'service_charge_is_inclusive' => config('restaurant.charges.service_charge_is_inclusive', false),
            ]);
        }

        return static::query()->first() ?? new static([
            'gst_percentage' => config('restaurant.charges.gst_percentage', 0),
            'gst_is_inclusive' => config('restaurant.charges.gst_is_inclusive', false),
            'service_charge_percentage' => config('restaurant.charges.service_charge_percentage', 0),
            'service_charge_is_inclusive' => config('restaurant.charges.service_charge_is_inclusive', false),
        ]);
    }
}
