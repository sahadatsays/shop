<?php

namespace App\Http\Requests\Admin;

use App\Models\Brand;

class UpdateBrandRequest extends BrandRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $brand = $this->route('brand');

        return $this->baseRules($brand instanceof Brand ? $brand->id : (int) $brand);
    }
}
