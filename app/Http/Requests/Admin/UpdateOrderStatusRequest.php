<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatus;
use App\Rules\ValidOrderStatusTransition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
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
            'status' => [
                'required',
                Rule::enum(OrderStatus::class),
                new ValidOrderStatusTransition($this->route('order')),
            ],
            'message' => ['nullable', 'string', 'max:500'],
            'author_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
