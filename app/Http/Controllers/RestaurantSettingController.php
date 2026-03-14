<?php

namespace App\Http\Controllers;

use App\Models\RestaurantSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class RestaurantSettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Index', [
            'settings' => RestaurantSetting::current(),
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
        ]);

        $settings = RestaurantSetting::query()->firstOrNew(['id' => 1]);
        $settings->fill([
            'gst_percentage' => round((float) $validated['gst_percentage'], 2),
            'gst_is_inclusive' => (bool) ($validated['gst_is_inclusive'] ?? false),
            'service_charge_percentage' => round((float) $validated['service_charge_percentage'], 2),
            'service_charge_is_inclusive' => (bool) ($validated['service_charge_is_inclusive'] ?? false),
        ]);
        $settings->save();

        return redirect()
            ->route('settings.index')
            ->with('success', 'Charge settings updated successfully');
    }
}
