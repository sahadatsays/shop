<?php

namespace App\Http\Requests\Admin;

class UpdateCustomerRequest extends CustomerRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules($this->route('customer')?->id);
    }
}
