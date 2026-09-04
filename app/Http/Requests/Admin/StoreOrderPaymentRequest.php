<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderPaymentRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', Rule::enum(PaymentMethod::class)],
            'transaction_reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'paid_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array{amount_cents: int, method: string, status: string, transaction_reference: ?string, notes: ?string, paid_at: ?string}
     */
    public function paymentData(): array
    {
        return [
            'amount_cents' => (int) round(((float) $this->input('amount')) * 100),
            'method' => $this->string('method')->toString(),
            'status' => PaymentStatus::Paid->value,
            'transaction_reference' => $this->string('transaction_reference')->toString() ?: null,
            'notes' => $this->string('notes')->toString() ?: null,
            'paid_at' => $this->input('paid_at'),
        ];
    }
}
