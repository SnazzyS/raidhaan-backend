<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $tableNames = collect($this->input('table_names', []))
            ->map(fn (mixed $tableName) => trim((string) $tableName))
            ->all();

        $this->merge([
            'table_names' => $tableNames,
        ]);
    }

    public function rules(): array
    {
        return [
            'gst_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'gst_is_inclusive' => ['nullable', 'boolean'],
            'service_charge_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'service_charge_is_inclusive' => ['nullable', 'boolean'],
            'table_names' => ['required', 'array', 'min:1'],
            'table_names.*' => ['required', 'string', 'max:50', 'distinct'],
        ];
    }
}
