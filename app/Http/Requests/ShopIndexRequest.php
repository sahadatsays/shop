<?php

namespace App\Http\Requests;

use App\Enums\ProductSort;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShopIndexRequest extends FormRequest
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
        $perPageOptions = config('shop.per_page_options', [12, 24, 36, 48]);

        return [
            'search' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable'],
            'category.*' => ['string', 'max:120'],
            'brand' => ['nullable'],
            'brand.*' => ['string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'in_stock' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'on_sale' => ['nullable', 'boolean'],
            'new_arrival' => ['nullable', 'boolean'],
            'sort' => ['nullable', Rule::in(ProductSort::values())],
            'per_page' => ['nullable', 'integer', Rule::in($perPageOptions)],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('category') && ! is_array($this->input('category'))) {
            $this->merge(['category' => [$this->input('category')]]);
        }

        if ($this->has('brand') && ! is_array($this->input('brand'))) {
            $this->merge(['brand' => [$this->input('brand')]]);
        }

        $this->merge([
            'featured' => $this->boolean('featured'),
            'on_sale' => $this->boolean('on_sale'),
            'new_arrival' => $this->boolean('new_arrival'),
            'in_stock' => $this->has('in_stock') ? $this->boolean('in_stock') : true,
        ]);
    }
}
