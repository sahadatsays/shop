<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends ProductRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('products.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
