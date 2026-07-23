<?php

namespace App\Http\Requests\Admin;

class StoreCustomerRequest extends CustomerRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
