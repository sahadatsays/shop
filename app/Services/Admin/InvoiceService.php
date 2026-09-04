<?php

namespace App\Services\Admin;

use App\Models\Invoice;
use App\Models\Order;
use App\Support\InvoiceNumberGenerator;
use App\Support\MoneyFormatter;
use App\Support\StoreSettings;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function generateForOrder(Order $order): Invoice
    {
        $existing = $order->invoice;

        if ($existing !== null) {
            return $existing;
        }

        return DB::transaction(function () use ($order): Invoice {
            /** @var Order $locked */
            $locked = Order::query()
                ->with(['customer', 'items', 'payments'])
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->invoice !== null) {
                return $locked->invoice;
            }

            $invoice = Invoice::query()->create([
                'order_id' => $locked->id,
                'invoice_number' => InvoiceNumberGenerator::generate(),
                'issued_at' => now(),
                'snapshot' => [],
            ]);

            $locked->setRelation('invoice', $invoice);
            $invoice->update([
                'snapshot' => $this->buildSnapshot($locked),
            ]);

            return $invoice->fresh() ?? $invoice;
        });
    }

    public function refreshSnapshot(Order $order): Invoice
    {
        $invoice = $order->invoice ?? $this->generateForOrder($order);
        $order = $order->loadMissing(['customer', 'items', 'payments']);

        $invoice->update([
            'snapshot' => $this->buildSnapshot($order),
        ]);

        return $invoice->fresh() ?? $invoice;
    }

    /**
     * @return array<string, mixed>
     */
    public function buildSnapshot(Order $order): array
    {
        $store = StoreSettings::current();
        $order->loadMissing(['customer', 'items', 'payments.receivedBy']);

        return [
            'store' => [
                'name' => $store->store_name,
                'address' => $store->address,
                'phone' => $store->phone,
                'email' => $store->support_email ?: $store->email,
                'logo_path' => $store->logo_path,
            ],
            'invoice_number' => $order->invoice?->invoice_number,
            'order_number' => $order->order_number,
            'issued_at' => ($order->invoice?->issued_at ?? now())->toIso8601String(),
            'customer' => [
                'name' => $order->customer->name,
                'email' => $order->customer->email,
                'phone' => $order->customer->phone,
            ],
            'shipping_address' => $order->shipping_address,
            'items' => $order->items->map(fn ($item): array => [
                'product_name' => $item->product_name ?: $item->product?->name,
                'sku' => $item->sku ?: $item->product?->sku,
                'quantity' => $item->quantity,
                'unit_price_cents' => $item->unit_price_cents,
                'discount_cents' => $item->discount_cents,
                'line_total_cents' => $item->line_total_cents,
            ])->values()->all(),
            'totals' => [
                'subtotal_cents' => $order->subtotal_cents,
                'discount_cents' => $order->discount_cents,
                'shipping_cents' => $order->shipping_cents,
                'tax_cents' => $order->tax_cents,
                'total_cents' => $order->total_cents,
                'paid_cents' => $order->paid_cents,
                'due_cents' => max(0, $order->total_cents - $order->paid_cents),
                'refunded_cents' => $order->refunded_cents,
            ],
            'payment' => [
                'status' => $order->payment_status->value,
                'status_label' => $order->payment_status->label(),
                'method' => $order->payment_method,
            ],
            'payments' => $order->payments->map(fn ($payment): array => [
                'amount_cents' => $payment->amount_cents,
                'amount' => MoneyFormatter::format($payment->amount_cents),
                'method' => $payment->method->label(),
                'status' => $payment->status->label(),
                'reference' => $payment->transaction_reference,
                'paid_at' => $payment->paid_at?->toIso8601String(),
                'received_by' => $payment->receivedBy?->name,
                'notes' => $payment->notes,
            ])->values()->all(),
            'notes' => $order->admin_notes,
            'terms' => 'Payment is due according to the recorded payment status. Returns are subject to store policy.',
        ];
    }
}
