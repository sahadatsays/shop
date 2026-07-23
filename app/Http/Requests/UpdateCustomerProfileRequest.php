<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateCustomerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('customer') !== null || session()->has('customer_id');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $customerId = $this->user('customer')?->getKey() ?? session('customer_id');

        return [
            'first_name' => ['sometimes', 'string', 'max:50'],
            'last_name' => ['sometimes', 'string', 'max:50'],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:30',
                Rule::unique('customers', 'phone')->ignore($customerId),
            ],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remove_avatar' => ['sometimes', 'boolean'],
            'current_password' => ['required_with:password', 'nullable', 'string'],
            'password' => ['sometimes', 'nullable', 'confirmed', Password::defaults()],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('first_name') || $this->filled('last_name')) {
            $this->merge([
                'name' => trim($this->string('first_name').' '.$this->string('last_name')),
            ]);
        }

        $this->merge([
            'remove_avatar' => $this->boolean('remove_avatar'),
        ]);
    }
}
