<?php

namespace App\Http\Requests\Admin;

class StoreProductRequest extends ProductRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
