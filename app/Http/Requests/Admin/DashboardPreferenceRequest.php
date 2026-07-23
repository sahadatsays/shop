<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DashboardPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('dashboard.view') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'widgets' => ['required', 'array'],
            'widgets.*.key' => ['required', 'string', 'exists:dashboard_widgets,key'],
            'widgets.*.position' => ['sometimes', 'integer', 'min:0'],
            'widgets.*.width' => ['sometimes', 'nullable', 'integer', 'min:3', 'max:12'],
            'widgets.*.is_visible' => ['sometimes', 'boolean'],
            'widgets.*.is_collapsed' => ['sometimes', 'boolean'],
            'widgets.*.is_pinned' => ['sometimes', 'boolean'],
        ];
    }
}
