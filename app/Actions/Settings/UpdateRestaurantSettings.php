<?php

namespace App\Actions\Settings;

use App\Models\Order;
use App\Models\RestaurantSetting;
use Illuminate\Support\Facades\Schema;

class UpdateRestaurantSettings
{
    public function handle(array $validated): array
    {
        if (! Schema::hasTable('restaurant_settings')) {
            return [
                'ok' => false,
                'message' => 'Run php artisan migrate first so charge settings can be saved.',
            ];
        }

        $settings = RestaurantSetting::query()->firstOrNew(['id' => 1]);
        $tableNames = array_values(array_filter(array_map(
            static fn ($tableName) => trim((string) $tableName),
            $validated['table_names']
        )));

        $activeTableNames = Order::query()
            ->where('delivery_type', 'dine_in')
            ->whereIn('status', ['pending', 'printed'])
            ->pluck('table_name')
            ->filter()
            ->unique()
            ->values()
            ->all();

        foreach ($activeTableNames as $activeTableName) {
            if (! in_array($activeTableName, $tableNames, true)) {
                return [
                    'ok' => false,
                    'message' => "Cannot remove {$activeTableName} while it has an active bill.",
                ];
            }
        }

        $settings->fill([
            'gst_percentage' => round((float) $validated['gst_percentage'], 2),
            'gst_is_inclusive' => (bool) ($validated['gst_is_inclusive'] ?? false),
            'service_charge_percentage' => round((float) $validated['service_charge_percentage'], 2),
            'service_charge_is_inclusive' => (bool) ($validated['service_charge_is_inclusive'] ?? false),
            'table_names' => $tableNames,
        ]);
        $settings->save();

        return [
            'ok' => true,
            'message' => 'Charge settings updated successfully',
        ];
    }
}
