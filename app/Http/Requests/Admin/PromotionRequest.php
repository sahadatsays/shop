<?php

namespace App\Http\Requests\Admin;

use App\Enums\PromotionPlacement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('promotions.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $promotionId = $this->route('promotion')?->id;
        $isCountdown = $this->routeIs('admin.countdown-promotions.*');

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('promotions', 'slug')->ignore($promotionId)],
            'placement' => ['required', Rule::enum(PromotionPlacement::class)],
            'headline' => ['required', 'string', 'max:255'],
            'subheadline' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:5000'],
            'cta_label' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'collection_id' => ['nullable', 'integer', 'exists:collections,id'],
            'offer_id' => ['nullable', 'integer', 'exists:offers,id'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => [$isCountdown ? 'required' : 'nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
