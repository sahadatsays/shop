<?php

namespace App\Http\Requests\Admin;

use App\Enums\Admin\DashboardWidgetType;
use App\Models\DashboardWidget;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DashboardWidgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('dashboard.widgets.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var DashboardWidget|null $widget */
        $widget = $this->route('dashboardWidget');

        return [
            'key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('dashboard_widgets', 'key')->ignore($widget?->id),
            ],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', Rule::enum(DashboardWidgetType::class)],
            'category' => ['required', 'string', 'max:40'],
            'width' => ['required', 'integer', 'min:3', 'max:12'],
            'height' => ['required', 'integer', 'min:1', 'max:6'],
            'display_order' => ['required', 'integer', 'min:0'],
            'refresh_interval' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'permission' => ['nullable', 'string', 'exists:permissions,slug'],
            'is_active' => ['sometimes', 'boolean'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();
        $data['is_active'] = $this->boolean('is_active');

        return $data;
    }
}
