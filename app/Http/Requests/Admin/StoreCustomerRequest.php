<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;

class StoreCustomerRequest extends CustomerRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('customers.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
