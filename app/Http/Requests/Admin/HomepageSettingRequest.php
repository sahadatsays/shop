<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomepageSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->hasPermission('homepage.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'featured_products_limit' => ['required', 'integer', 'min:1', 'max:24'],
            'new_arrivals_limit' => ['required', 'integer', 'min:1', 'max:24'],
            'best_sellers_limit' => ['required', 'integer', 'min:1', 'max:24'],
            'brands_limit' => ['required', 'integer', 'min:1', 'max:24'],
            'categories_limit' => ['required', 'integer', 'min:1', 'max:24'],
            'reviews_limit' => ['required', 'integer', 'min:1', 'max:24'],
            'new_badge_days' => ['required', 'integer', 'min:1', 'max:365'],
            'hide_out_of_stock' => ['nullable', 'boolean'],
            'enabled_sections' => ['nullable', 'array'],
            'enabled_sections.*' => ['string'],
            'popular_searches' => ['nullable', 'string', 'max:1000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ];
    }
}
