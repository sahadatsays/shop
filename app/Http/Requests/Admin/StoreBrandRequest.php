<?php

namespace App\Http\Requests\Admin;

class StoreBrandRequest extends BrandRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
