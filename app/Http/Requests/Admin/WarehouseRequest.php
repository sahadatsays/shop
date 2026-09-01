<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

abstract class WarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('warehouses.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseRules(?int $warehouseId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('warehouses', 'code')->ignore($warehouseId),
            ],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:2'],
            'address' => ['nullable', 'string', 'max:1000'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper((string) $this->input('code', '')),
            'is_default' => $this->boolean('is_default'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
