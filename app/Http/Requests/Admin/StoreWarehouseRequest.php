<?php

namespace App\Http\Requests\Admin;

class StoreWarehouseRequest extends WarehouseRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
