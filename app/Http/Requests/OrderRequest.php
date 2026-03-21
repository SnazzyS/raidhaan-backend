<?php

namespace App\Http\Requests;

use App\Actions\Settings\ResolveRestaurantSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $order = (array) $this->input('order', []);
        $phoneNumber = trim((string) $this->input('phone_number'));
        $address = trim((string) $this->input('address'));
        $city = strtolower(trim((string) $this->input('city')));
        $tableName = trim((string) ($order['table_name'] ?? ''));
        $transferReferenceNumber = trim((string) ($order['transfer_reference_number'] ?? ''));

        $this->merge([
            'phone_number' => $phoneNumber !== '' ? $phoneNumber : null,
            'address' => $address !== '' ? $address : null,
            'city' => $city !== '' ? $city : null,
            'order' => array_merge($order, [
                'table_name' => $tableName !== '' ? $tableName : null,
                'transfer_reference_number' => $transferReferenceNumber !== '' ? $transferReferenceNumber : null,
                'discount_type' => filled($order['discount_type'] ?? null) ? $order['discount_type'] : null,
                'discount_value' => filled($order['discount_type'] ?? null)
                    ? round((float) ($order['discount_value'] ?? 0), 2)
                    : 0,
            ]),
        ]);
    }

    public function rules(): array
    {
        $deliveryType = $this->input('order.delivery_type');
        $paymentMethod = $this->input('order.payment_method');
        $discountType = $this->input('order.discount_type');

        return [
            'phone_number' => ['nullable', 'string', 'max:20', Rule::requiredIf($deliveryType !== 'dine_in')],
            'address' => ['nullable', 'string', 'max:255', Rule::requiredIf($deliveryType !== 'dine_in')],
            'city' => ['nullable', Rule::in(['male', 'hulhumale phase 1', 'hulhumale phase 2']), Rule::requiredIf($deliveryType !== 'dine_in')],
            'order.status' => ['required', Rule::in(['pending', 'printed', 'completed', 'cancelled', 'voided'])],
            'order.delivery_type' => ['required', Rule::in(['delivery', 'pickup', 'dine_in'])],
            'order.table_name' => [
                'nullable',
                'string',
                Rule::requiredIf($deliveryType === 'dine_in'),
                Rule::in($this->tableNames()),
            ],
            'order.payment_method' => ['required', Rule::in(['transfer', 'cash', 'card'])],
            'order.transfer_reference_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf($paymentMethod === 'transfer'),
            ],
            'order.discount_type' => ['nullable', Rule::in(['fixed', 'percentage'])],
            'order.discount_value' => ['nullable', 'numeric', 'min:0', Rule::requiredIf(filled($discountType))],
            'order.items' => ['required', 'array', 'min:1'],
            'order.items.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'order.items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (
                $this->input('order.discount_type') === 'percentage'
                && (float) $this->input('order.discount_value', 0) > 100
            ) {
                $validator->errors()->add('order.discount_value', 'Percentage discounts cannot exceed 100%.');
            }
        });
    }

    private function tableNames(): array
    {
        return app(ResolveRestaurantSettings::class)->handle()['table_names'];
    }
}
