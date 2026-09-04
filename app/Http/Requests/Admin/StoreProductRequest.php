<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends ProductRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->can('create', Product::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
