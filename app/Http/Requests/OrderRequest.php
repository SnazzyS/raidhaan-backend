<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'integer', 'digits:7'],
            'address' => ['required', 'string'],
            'city' => 'required', Rule::in(['male', 'hulhumale phase 1', 'hulhumale phase 2']),
            'order.status' => ['required', Rule::in(['pending', 'completed', 'cancelled'])],
            'order.delivery_type' => ['required', Rule::in(['delivery', 'pickup'])],
            'order.payment_method' => ['required', Rule::in(['transfer', 'cash', 'card'])],
            'order.transfer_reference_number' => ['string', 'nullable'],
            'order.items' => ['required', 'array'],
            'order.items.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'order.items.*.quantity' => ['required', 'integer']
        ];
    }
}
