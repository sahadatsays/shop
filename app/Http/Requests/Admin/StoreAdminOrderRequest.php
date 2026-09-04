<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderSource;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('idempotency_key')) {
            $this->merge([
                'idempotency_key' => (string) str()->uuid(),
            ]);
        }

        $items = collect($this->input('items', []))
            ->filter(fn ($item): bool => filled($item['product_id'] ?? null) && (int) ($item['quantity'] ?? 0) > 0)
            ->values()
            ->all();

        $this->merge(['items' => $items]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_mode' => ['required', Rule::in(['existing', 'new'])],
            'customer_id' => ['required_if:customer_mode,existing', 'nullable', 'integer', 'exists:customers,id'],
            'new_customer.name' => ['required_if:customer_mode,new', 'nullable', 'string', 'max:120'],
            'new_customer.email' => ['required_if:customer_mode,new', 'nullable', 'email', 'max:255', 'unique:customers,email'],
            'new_customer.phone' => ['nullable', 'string', 'max:40'],
            'source' => ['required', Rule::enum(OrderSource::class)],
            'shipping_method' => ['nullable', 'string', 'max:100'],
            'shipping_cents' => ['required', 'integer', 'min:0'],
            'order_discount_type' => ['nullable', Rule::in(['fixed', 'percent'])],
            'order_discount_value' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'initial_payment_cents' => ['nullable', 'integer', 'min:0'],
            'transaction_reference' => ['nullable', 'string', 'max:120'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'idempotency_key' => ['required', 'string', 'max:64'],
            'shipping_address.first_name' => ['required', 'string', 'max:80'],
            'shipping_address.last_name' => ['required', 'string', 'max:80'],
            'shipping_address.line1' => ['required', 'string', 'max:255'],
            'shipping_address.line2' => ['nullable', 'string', 'max:255'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.state' => ['required', 'string', 'max:100'],
            'shipping_address.postal_code' => ['required', 'string', 'max:30'],
            'shipping_address.country' => ['required', 'string', 'max:100'],
            'shipping_address.phone' => ['nullable', 'string', 'max:40'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'items.*.unit_price_cents' => ['nullable', 'integer', 'min:0'],
            'items.*.discount_cents' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one product to the order.',
            'customer_id.required_if' => 'Select a customer for this order.',
            'new_customer.email.unique' => 'A customer with this email already exists. Select them instead.',
        ];
    }
}
