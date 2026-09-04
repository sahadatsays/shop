<?php

namespace App\Services\Admin;

use App\Enums\AuditAction;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderPaymentService
{
    public function __construct(
        private InvoiceService $invoices,
        private AuditService $audit,
    ) {}

    /**
     * @param  array{
     *     amount_cents: int,
     *     method: string,
     *     status?: string,
     *     transaction_reference?: ?string,
     *     notes?: ?string,
     *     paid_at?: ?string
     * }  $data
     */
    public function record(Order $order, array $data, User $admin, bool $syncInvoice = true): Payment
    {
        $amountCents = (int) $data['amount_cents'];
        $status = PaymentStatus::tryFrom((string) ($data['status'] ?? PaymentStatus::Paid->value)) ?? PaymentStatus::Paid;

        if ($amountCents <= 0) {
            throw ValidationException::withMessages([
                'amount_cents' => 'Payment amount must be greater than zero.',
            ]);
        }

        $payment = DB::transaction(function () use ($order, $data, $admin, $amountCents, $status): Payment {
            /** @var Order $locked */
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (in_array($locked->payment_status, [PaymentStatus::Refunded], true)) {
                throw ValidationException::withMessages([
                    'amount_cents' => 'Cannot record payments on a fully refunded order.',
                ]);
            }

            $projectedPaid = $locked->paid_cents;

            if ($status === PaymentStatus::Paid) {
                $projectedPaid += $amountCents;
            }

            if ($projectedPaid > $locked->total_cents) {
                throw ValidationException::withMessages([
                    'amount_cents' => 'Paid amount cannot exceed the order total.',
                ]);
            }

            $payment = Payment::query()->create([
                'order_id' => $locked->id,
                'amount_cents' => $amountCents,
                'method' => PaymentMethod::from($data['method']),
                'status' => $status,
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'paid_at' => $status === PaymentStatus::Paid
                    ? ($data['paid_at'] ?? now())
                    : null,
                'received_by' => $admin->id,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->syncOrderPaymentState($locked);

            return $payment->fresh(['receivedBy']) ?? $payment;
        });

        $this->audit->log(
            AuditAction::OrderPaymentRecorded,
            "Payment of {$payment->amount_cents} cents recorded on order {$order->order_number}.",
            $order,
            $admin,
            [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'amount_cents' => $payment->amount_cents,
                'method' => $payment->method->value,
            ],
        );

        if ($syncInvoice) {
            $this->invoices->refreshSnapshot($order->fresh(['items', 'payments', 'customer', 'invoice']) ?? $order);
        }

        return $payment;
    }

    public function syncOrderPaymentState(Order $order): void
    {
        $paidCents = (int) $order->payments()
            ->whereIn('status', [
                PaymentStatus::Paid->value,
                PaymentStatus::PartiallyRefunded->value,
            ])
            ->sum('amount_cents');

        $paidCents = max(0, min($paidCents, $order->total_cents));

        $paymentStatus = match (true) {
            $order->refunded_cents >= $order->total_cents && $order->total_cents > 0 => PaymentStatus::Refunded,
            $order->refunded_cents > 0 && $paidCents > 0 => PaymentStatus::PartiallyRefunded,
            $paidCents >= $order->total_cents && $order->total_cents > 0 => PaymentStatus::Paid,
            $paidCents > 0 => PaymentStatus::PartiallyPaid,
            default => PaymentStatus::Pending,
        };

        $latestMethod = $order->payments()
            ->where('status', PaymentStatus::Paid->value)
            ->latest('paid_at')
            ->first();

        $order->forceFill([
            'paid_cents' => $paidCents,
            'payment_status' => $paymentStatus,
            'payment_method' => $latestMethod?->method->label() ?? $order->payment_method,
            'payment_reference' => $latestMethod?->transaction_reference ?? $order->payment_reference,
        ])->save();
    }
}
