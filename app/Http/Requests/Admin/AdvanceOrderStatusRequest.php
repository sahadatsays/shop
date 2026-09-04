<?php

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class AdvanceOrderStatusRequest extends FormRequest
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
        return [];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Order $order */
            $order = $this->route('order');

            if (! $order->status->canAdvanceProgress()) {
                $validator->errors()->add(
                    'status',
                    'This order has no next fulfillment status.',
                );
            }
        });
    }
}
