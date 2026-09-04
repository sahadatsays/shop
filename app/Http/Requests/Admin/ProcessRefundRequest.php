<?php

namespace App\Http\Requests\Admin;

use App\Enums\RefundReason;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProcessRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('refunds.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'refund_amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', Rule::enum(RefundReason::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'restore_stock' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'restore_stock' => $this->boolean('restore_stock', config('refunds.default_restore_stock', true)),
            'amount_cents' => (int) round(((float) $this->input('refund_amount', 0)) * 100),
        ]);
    }

    public function amountCents(): int
    {
        return (int) $this->input('amount_cents', 0);
    }
}
