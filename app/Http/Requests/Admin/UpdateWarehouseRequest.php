<?php

namespace App\Http\Requests\Admin;

class UpdateWarehouseRequest extends WarehouseRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules($this->route('warehouse')?->id);
    }
}
