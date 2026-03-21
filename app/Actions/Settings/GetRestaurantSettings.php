<?php

namespace App\Actions\Settings;

class GetRestaurantSettings
{
    public function __construct(
        private ResolveRestaurantSettings $resolveRestaurantSettings,
    ) {}

    public function handle(): array
    {
        $resolvedSettings = $this->resolveRestaurantSettings->handle();
        $settings = $resolvedSettings['settings'];

        return [
            'settings' => [
                'gst_percentage' => (float) $settings->gst_percentage,
                'gst_is_inclusive' => (bool) $settings->gst_is_inclusive,
                'service_charge_percentage' => (float) $settings->service_charge_percentage,
                'service_charge_is_inclusive' => (bool) $settings->service_charge_is_inclusive,
                'table_names' => $resolvedSettings['table_names'],
            ],
        ];
    }
}
