<?php

namespace App\Http\Requests\Admin;

class UpdateProductRequest extends ProductRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules($this->route('product')?->id);
    }
}
