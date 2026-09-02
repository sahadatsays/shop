<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;

class StoreBrandRequest extends BrandRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('brands.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
