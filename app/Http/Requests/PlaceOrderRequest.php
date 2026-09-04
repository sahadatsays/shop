<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'shipping.phone' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('customers', 'phone')
                    ->whereNotNull('phone')
                    ->ignore(Auth::guard('customer')->id()),
            ],
            'delivery_method' => ['required', 'string', Rule::in(array_keys(config('cart.shipping_methods', [])))],
            'payment_method' => ['required', 'string', Rule::in(['cod'])],
            'terms_accepted' => ['accepted'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Customer|null $customer */
            $customer = Auth::guard('customer')->user();

            if ($customer instanceof Customer) {
                $email = strtolower((string) $this->input('email'));

                if ($customer->email !== $email) {
                    $validator->errors()->add(
                        'email',
                        'Checkout must use your account email. Update it from your profile if needed.',
                    );
                }

                return;
            }

            $email = strtolower((string) $this->input('email'));

            if (Customer::query()->where('email', $email)->exists()) {
                $validator->errors()->add(
                    'email',
                    'An account with this email already exists. Please sign in to complete your order.',
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_method.in' => 'Online payment is under construction. Please choose cash on delivery.',
            'terms_accepted.accepted' => 'You must accept the terms and policies before placing your order.',
            'shipping.phone.unique' => 'This phone number is already associated with another account.',
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
}
