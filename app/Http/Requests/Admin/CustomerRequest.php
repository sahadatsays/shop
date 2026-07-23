<?php

namespace App\Http\Requests\Admin;

use App\Enums\AddressType;
use App\Enums\CustomerStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseRules(?int $customerId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customerId)->whereNull('deleted_at'),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::enum(CustomerStatus::class)],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
            'note' => ['nullable', 'string', 'max:2000'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.label' => ['nullable', 'string', 'max:100'],
            'addresses.*.type' => ['nullable', Rule::enum(AddressType::class)],
            'addresses.*.name' => ['nullable', 'string', 'max:255'],
            'addresses.*.phone' => ['nullable', 'string', 'max:50'],
            'addresses.*.line1' => ['nullable', 'string', 'max:255'],
            'addresses.*.line2' => ['nullable', 'string', 'max:255'],
            'addresses.*.city' => ['nullable', 'string', 'max:100'],
            'addresses.*.state' => ['nullable', 'string', 'max:100'],
            'addresses.*.postal_code' => ['nullable', 'string', 'max:30'],
            'addresses.*.country' => ['nullable', 'string', 'max:2'],
            'addresses.*.is_default' => ['sometimes', 'boolean'],
        ];
    }
}
