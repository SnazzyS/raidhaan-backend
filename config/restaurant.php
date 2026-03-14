<?php

$defaultTables = [];

for ($tableNumber = 1; $tableNumber <= 12; $tableNumber++) {
    $defaultTables[] = "Table {$tableNumber}";
}

$configuredTables = array_values(array_filter(array_map(
    static fn (string $tableName) => trim($tableName),
    explode(',', env('RESTAURANT_TABLES', implode(',', $defaultTables)))
)));

return [
    'tables' => $configuredTables ?: $defaultTables,
    'charges' => [
        'gst_percentage' => (float) env('RESTAURANT_GST_PERCENTAGE', 0),
        'gst_is_inclusive' => filter_var(env('RESTAURANT_GST_INCLUDED', false), FILTER_VALIDATE_BOOLEAN),
        'service_charge_percentage' => (float) env('RESTAURANT_SERVICE_CHARGE_PERCENTAGE', 0),
        'service_charge_is_inclusive' => filter_var(env('RESTAURANT_SERVICE_CHARGE_INCLUDED', false), FILTER_VALIDATE_BOOLEAN),
    ],
];
