<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;

class StoreCategoryRequest extends CategoryRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('categories.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }

    public function withValidator($validator): void
    {
        $this->withParentValidation($validator);
    }
}
