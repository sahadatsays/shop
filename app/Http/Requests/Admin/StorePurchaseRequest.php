<?php

namespace App\Http\Requests\Admin;

use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('purchases.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'integer', Rule::exists('suppliers', 'id')->whereNull('deleted_at')],
            'purchase_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'discount_cents' => ['nullable', 'integer', 'min:0'],
            'shipping_cents' => ['nullable', 'integer', 'min:0'],
            'tax_cents' => ['nullable', 'integer', 'min:0'],
            'submit' => ['sometimes', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', Rule::exists('products', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost_cents' => ['required', 'integer', 'min:0'],
            'items.*.discount_cents' => ['nullable', 'integer', 'min:0'],
            'items.*.tax_cents' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'submit' => $this->boolean('submit'),
            'discount_cents' => (int) $this->input('discount_cents', 0),
            'shipping_cents' => (int) $this->input('shipping_cents', 0),
            'tax_cents' => (int) $this->input('tax_cents', 0),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->filled('supplier_id')) {
                return;
            }

            $supplier = Supplier::query()->find($this->integer('supplier_id'));

            if ($supplier && ! $supplier->isSelectableForPurchase()) {
                $validator->errors()->add('supplier_id', 'Inactive suppliers cannot be selected for new purchases.');
            }
        });
    }
}
