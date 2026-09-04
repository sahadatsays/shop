<?php

namespace App\Http\Requests\Admin;

class UpdateSupplierRequest extends SupplierRequest
{
    protected function permissionSlug(): string
    {
        return 'suppliers.edit';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
