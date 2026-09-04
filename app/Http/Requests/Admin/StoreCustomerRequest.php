<?php

namespace App\Http\Requests\Admin;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class StoreCustomerRequest extends CustomerRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->can('create', Customer::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
