<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
        ]);
    }

    public function rules(): array
    {
        $categoryId = $this->route('category') ? $this->route('category')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, callable $fail) use ($categoryId) {
                    if (! is_string($value) || $value === '') {
                        return;
                    }

                    $normalizedName = Str::lower($value);
                    $query = Category::query()->whereRaw('LOWER(name) = ?', [$normalizedName]);

                    if ($categoryId) {
                        $query->where('id', '!=', $categoryId);
                    }

                    if ($query->exists()) {
                        $fail('The ' . $attribute . ' has already been taken.');
                    }
                },
            ],
        ];
    }
}
