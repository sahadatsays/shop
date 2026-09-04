<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;

class UpdateCustomerRequest extends CustomerRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->can('update', $this->route('customer')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules($this->route('customer')?->id);
    }
}
