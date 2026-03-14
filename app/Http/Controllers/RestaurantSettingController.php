<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\RestaurantSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RestaurantSettingController extends Controller
{
    public function index()
    {
        $settings = RestaurantSetting::current();

        return Inertia::render('Settings/Index', [
            'settings' => [
                'gst_percentage' => (float) $settings->gst_percentage,
                'gst_is_inclusive' => (bool) $settings->gst_is_inclusive,
                'service_charge_percentage' => (float) $settings->service_charge_percentage,
                'service_charge_is_inclusive' => (bool) $settings->service_charge_is_inclusive,
                'table_names' => $settings->resolvedTableNames(),
            ],
        ]);
    }

    public function update(Request $request)
    {
        if (! Schema::hasTable('restaurant_settings')) {
            return redirect()
                ->route('settings.index')
                ->with('error', 'Run php artisan migrate first so charge settings can be saved.');
        }

        $validated = $request->validate([
            'gst_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'gst_is_inclusive' => ['nullable', 'boolean'],
            'service_charge_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'service_charge_is_inclusive' => ['nullable', 'boolean'],
            'table_names' => ['required', 'array', 'min:1'],
            'table_names.*' => ['required', 'string', 'max:50', 'distinct'],
        ]);

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
                return redirect()
                    ->route('settings.index')
                    ->with('error', "Cannot remove {$activeTableName} while it has an active bill.");
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

        return redirect()
            ->route('settings.index')
            ->with('success', 'Charge settings updated successfully');
    }
}
