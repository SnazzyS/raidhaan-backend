<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleFiltersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'payment_method' => ['nullable', Rule::in(['cash', 'card', 'transfer'])],
            'delivery_type' => ['nullable', Rule::in(['dine_in', 'delivery', 'pickup'])],
        ];
    }
}
