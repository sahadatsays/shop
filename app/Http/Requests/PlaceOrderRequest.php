<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $billingRequired = ! $this->boolean('billing_same_as_shipping');

        return [
            'email' => ['required', 'email', 'max:255'],
            'shipping' => ['required', 'array'],
            'shipping.first_name' => ['required', 'string', 'max:100'],
            'shipping.last_name' => ['required', 'string', 'max:100'],
            'shipping.line1' => ['required', 'string', 'max:255'],
            'shipping.line2' => ['nullable', 'string', 'max:255'],
            'shipping.city' => ['required', 'string', 'max:120'],
            'shipping.state' => ['required', 'string', 'max:120'],
            'shipping.postal_code' => ['required', 'string', 'max:20'],
            'shipping.country' => ['required', 'string', 'max:120'],
            'shipping.phone' => ['nullable', 'string', 'max:30'],
            'billing_same_as_shipping' => ['sometimes', 'boolean'],
            'billing' => [Rule::requiredIf($billingRequired), 'nullable', 'array'],
            'billing.first_name' => [Rule::requiredIf($billingRequired), 'nullable', 'string', 'max:100'],
            'billing.last_name' => [Rule::requiredIf($billingRequired), 'nullable', 'string', 'max:100'],
            'billing.line1' => [Rule::requiredIf($billingRequired), 'nullable', 'string', 'max:255'],
            'billing.line2' => ['nullable', 'string', 'max:255'],
            'billing.city' => [Rule::requiredIf($billingRequired), 'nullable', 'string', 'max:120'],
            'billing.state' => [Rule::requiredIf($billingRequired), 'nullable', 'string', 'max:120'],
            'billing.postal_code' => [Rule::requiredIf($billingRequired), 'nullable', 'string', 'max:20'],
            'billing.country' => [Rule::requiredIf($billingRequired), 'nullable', 'string', 'max:120'],
            'delivery_method' => ['required', 'string', Rule::in(array_keys(config('cart.shipping_methods', [])))],
            'payment_method' => ['required', 'string', Rule::in(['card', 'paypal', 'applepay'])],
            'terms_accepted' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'terms_accepted.accepted' => 'You must accept the terms and policies before placing your order.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function shippingAddress(): array
    {
        $shipping = $this->validated('shipping');

        return [
            'first_name' => $shipping['first_name'],
            'last_name' => $shipping['last_name'],
            'line1' => $shipping['line1'],
            'line2' => $shipping['line2'] ?? null,
            'city' => $shipping['city'],
            'state' => $shipping['state'],
            'postal_code' => $shipping['postal_code'],
            'country' => $shipping['country'],
            'phone' => $shipping['phone'] ?? null,
            'email' => $this->validated('email'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function billingAddress(): array
    {
        if ($this->boolean('billing_same_as_shipping')) {
            $address = $this->shippingAddress();
            unset($address['email'], $address['phone']);

            return $address;
        }

        $billing = $this->validated('billing');

        return [
            'first_name' => $billing['first_name'],
            'last_name' => $billing['last_name'],
            'line1' => $billing['line1'],
            'line2' => $billing['line2'] ?? null,
            'city' => $billing['city'],
            'state' => $billing['state'],
            'postal_code' => $billing['postal_code'],
            'country' => $billing['country'],
        ];
    }
}
