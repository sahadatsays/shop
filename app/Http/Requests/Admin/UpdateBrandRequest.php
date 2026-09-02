<?php

namespace App\Http\Requests\Admin;

use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class UpdateBrandRequest extends BrandRequest
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
        $brand = $this->route('brand');

        return $this->baseRules($brand instanceof Brand ? $brand->id : (int) $brand);
    }
}
