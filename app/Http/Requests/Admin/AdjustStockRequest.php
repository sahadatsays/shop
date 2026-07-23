<?php

namespace App\Http\Requests\Admin;

use App\Enums\StockMovementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdjustStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'type' => [
                'required',
                Rule::in(array_map(fn (StockMovementType $type): string => $type->value, StockMovementType::adjustable())),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
