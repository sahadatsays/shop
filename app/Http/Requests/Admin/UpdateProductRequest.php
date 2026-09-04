<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends ProductRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->can('update', $this->route('product')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules($this->route('product')?->id);
    }
}
