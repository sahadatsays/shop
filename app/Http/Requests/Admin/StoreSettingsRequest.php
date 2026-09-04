<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('settings.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:60'],
            'address' => ['nullable', 'string', 'max:500'],
            'support_email' => ['nullable', 'email', 'max:120'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'social_x' => ['nullable', 'url', 'max:255'],
            'currency' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'string', Rule::in(timezone_identifiers_list())],
            'mail_from_name' => ['nullable', 'string', 'max:120'],
            'mail_from_address' => ['nullable', 'email', 'max:120'],
            'maintenance_enabled' => ['sometimes', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:1000'],
            'maintenance_secret' => ['nullable', 'string', 'max:32'],
            'meta_title' => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'og_image' => ['nullable', 'image', 'max:4096'],
            'utility_bar_message' => ['nullable', 'string', 'max:255'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'flat_shipping' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'inside_dhaka_shipping' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'outside_dhaka_shipping' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'google_analytics_id' => ['nullable', 'string', 'max:32'],
            'theme_colors' => ['nullable', 'array'],
            'theme_colors.*' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'maintenance_enabled' => $this->boolean('maintenance_enabled'),
        ]);
    }
}
