<?php

namespace App\Actions\Settings;

use App\Models\RestaurantSetting;
use Illuminate\Support\Facades\Schema;

class ResolveRestaurantSettings
{
    public function handle(): array
    {
        $settings = ! Schema::hasTable('restaurant_settings')
            ? new RestaurantSetting($this->defaults())
            : RestaurantSetting::query()->first() ?? new RestaurantSetting($this->defaults());

        return [
            'settings' => $settings,
            'table_names' => $this->normalizeTableNames($settings->table_names ?? []),
        ];
    }

    private function defaults(): array
    {
        return [
            'gst_percentage' => config('restaurant.charges.gst_percentage', 0),
            'gst_is_inclusive' => config('restaurant.charges.gst_is_inclusive', false),
            'service_charge_percentage' => config('restaurant.charges.service_charge_percentage', 0),
            'service_charge_is_inclusive' => config('restaurant.charges.service_charge_is_inclusive', false),
            'table_names' => config('restaurant.tables', []),
        ];
    }

    private function normalizeTableNames(array $tableNames): array
    {
        $normalized = array_values(array_filter(array_map(
            static fn ($tableName) => trim((string) $tableName),
            $tableNames
        )));

        return $normalized ?: config('restaurant.tables', []);
    }
}
