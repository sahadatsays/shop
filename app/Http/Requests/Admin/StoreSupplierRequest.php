<?php

namespace App\Http\Requests\Admin;

class StoreSupplierRequest extends SupplierRequest
{
    protected function permissionSlug(): string
    {
        return 'suppliers.create';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
