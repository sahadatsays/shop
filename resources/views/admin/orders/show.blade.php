@php
    use App\Support\MoneyFormatter;
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header :title="$order->order_number" description="Order details, lifecycle status, timeline, and admin notes.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.orders.index')">Back</x-admin.button>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.orders.invoice', $order)" target="_blank">Invoice</x-admin.button>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.customers.show', $order->customer)">View customer</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Items" :value="(string) $order->items->sum('quantity')" />
        <x-admin.stat-card label="Order total" :value="MoneyFormatter::format($order->total_cents)" />
        <x-admin.stat-card label="Paid / Due" :value="MoneyFormatter::format($order->paid_cents).' / '.MoneyFormatter::format($order->dueCents())" />
        <x-admin.stat-card label="Status" :value="$order->status->label()" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Order summary">
                <dl>
                    <x-admin.detail-row label="Order number">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $order->order_number }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Customer">
                        <a href="{{ route('admin.customers.show', $order->customer) }}" class="text-sm font-medium admin-text hover:text-admin-brand">{{ $order->customer->name }}</a>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Email" :value="$order->customer->email" />
                    <x-admin.detail-row label="Placed" :value="$order->placed_at?->format('M j, Y g:i A')" />
                    <x-admin.detail-row label="Subtotal" :value="MoneyFormatter::format($order->subtotal_cents)" />
                    <x-admin.detail-row label="Discount" :value="MoneyFormatter::format($order->discount_cents)" />
                    <x-admin.detail-row label="Shipping" :value="MoneyFormatter::format($order->shipping_cents)" />
                    <x-admin.detail-row label="Tax" :value="MoneyFormatter::format($order->tax_cents)" />
                    <x-admin.detail-row label="Total" :value="MoneyFormatter::format($order->total_cents)" />
                    <x-admin.detail-row label="Paid" :value="MoneyFormatter::format($order->paid_cents)" />
                    <x-admin.detail-row label="Due" :value="MoneyFormatter::format($order->dueCents())" />
                    <x-admin.detail-row label="Payment">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-admin.badge :variant="$order->payment_status->badgeVariant()" dot>{{ $order->payment_status->label() }}</x-admin.badge>
                            <span class="text-sm admin-text-secondary">{{ $order->payment_method ?? '—' }}</span>
                        </div>
                    </x-admin.detail-row>
                    @if ($order->source)
                        <x-admin.detail-row label="Source" :value="$order->source->label()" />
                    @endif
                    @if ($order->invoice)
                        <x-admin.detail-row label="Invoice">
                            <a href="{{ route('admin.orders.invoice', $order) }}" class="text-sm font-medium admin-text hover:text-admin-brand">{{ $order->invoice->invoice_number }}</a>
                        </x-admin.detail-row>
                    @endif
                    @if ($order->refunded_cents > 0)
                        <x-admin.detail-row label="Refunded" :value="MoneyFormatter::format($order->refunded_cents)" />
                    @endif
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$order->status->badgeVariant()" dot>{{ $order->status->label() }}</x-admin.badge>
                    </x-admin.detail-row>
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="Line items">
                @if ($order->items->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b admin-border bg-admin-bg/40">
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Product</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">SKU</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Qty</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Unit</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Line total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-admin-border/60">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-medium admin-text">{{ $item->displayName() }}</td>
                                        <td class="px-4 py-3 font-mono text-xs admin-text-secondary">{{ $item->displaySku() }}</td>
                                        <td class="px-4 py-3 tabular-nums admin-text-secondary">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 tabular-nums admin-text-secondary">{{ MoneyFormatter::format($item->unit_price_cents) }}</td>
                                        <td class="px-4 py-3 tabular-nums font-medium admin-text">{{ MoneyFormatter::format($item->line_total_cents) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm admin-muted">No line items recorded.</p>
                @endif
            </x-admin.form-card>

            <x-admin.form-card title="Order timeline" description="Status changes and fulfillment milestones.">
                <ul class="space-y-0">
                    @forelse ($order->timelineEvents as $event)
                        <li class="relative border-l-2 admin-border pb-6 pl-6 last:pb-0">
                            <span class="absolute -left-[9px] top-1 flex size-4 items-center justify-center rounded-full border-2 border-admin-brand bg-admin-surface"></span>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-admin.badge :variant="$event->status->badgeVariant()" dot>{{ $event->status->label() }}</x-admin.badge>
                                <span class="text-xs admin-muted">{{ $event->created_at?->format('M j, Y g:i A') }}</span>
                            </div>
                            @if ($event->message)
                                <p class="mt-2 text-sm admin-text">{{ $event->message }}</p>
                            @endif
                            <p class="mt-1 text-xs admin-muted">{{ $event->author_name ?: 'Admin' }}</p>
                        </li>
                    @empty
                        <li class="text-sm admin-muted">No timeline events yet.</li>
                    @endforelse
                </ul>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Payments" description="Record partial or full payments against this order.">
                <ul class="mb-4 space-y-3">
                    @forelse ($order->payments as $payment)
                        <li class="rounded-[var(--radius-admin)] border admin-border p-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-medium tabular-nums admin-text">{{ MoneyFormatter::format($payment->amount_cents) }}</span>
                                <x-admin.badge :variant="$payment->status->badgeVariant()" dot>{{ $payment->status->label() }}</x-admin.badge>
                            </div>
                            <p class="mt-1 text-xs admin-muted">
                                {{ $payment->method->label() }}
                                @if ($payment->transaction_reference)
                                    · {{ $payment->transaction_reference }}
                                @endif
                                · {{ $payment->paid_at?->format('M j, Y g:i A') }}
                                @if ($payment->receivedBy)
                                    · {{ $payment->receivedBy->name }}
                                @endif
                            </p>
                        </li>
                    @empty
                        <li class="text-sm admin-muted">No payments recorded yet.</li>
                    @endforelse
                </ul>

                @if (auth('admin')->user()?->hasPermission('orders.manage') && $order->dueCents() > 0)
                    <form method="POST" action="{{ route('admin.orders.payments.store', $order) }}" class="space-y-4 border-t admin-border pt-4">
                        @csrf
                        <x-admin.input
                            label="Amount"
                            name="amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            :value="old('amount', number_format($order->dueCents() / 100, 2, '.', ''))"
                            required
                        />
                        <x-admin.select label="Method" name="method" required>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->value }}" @selected(old('method') === $method->value)>{{ $method->label() }}</option>
                            @endforeach
                        </x-admin.select>
                        <x-admin.input label="Reference" name="transaction_reference" :value="old('transaction_reference')" />
                        <x-admin.textarea label="Notes" name="notes" rows="2">{{ old('notes') }}</x-admin.textarea>
                        <x-admin.button type="submit" size="sm" class="w-full">Record payment</x-admin.button>
                    </form>
                @endif
            </x-admin.form-card>

            <x-admin.form-card title="Update status">
                <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <x-admin.select label="Status" name="status" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $order->status->value) === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </x-admin.select>
                    <x-admin.textarea
                        label="Timeline note"
                        name="message"
                        rows="3"
                        placeholder="Optional note for the timeline…"
                    >{{ old('message') }}</x-admin.textarea>
                    <x-admin.button type="submit" size="sm" class="w-full">Update status</x-admin.button>
                </form>
            </x-admin.form-card>

            <x-admin.form-card title="Add admin note">
                <form method="POST" action="{{ route('admin.orders.notes.store', $order) }}" class="space-y-4">
                    @csrf
                    <x-admin.textarea
                        label="Note"
                        name="body"
                        rows="4"
                        placeholder="Internal note for this order…"
                        required
                    >{{ old('body') }}</x-admin.textarea>
                    <x-admin.button type="submit" size="sm" class="w-full">Save note</x-admin.button>
                </form>
            </x-admin.form-card>

            @if ($order->refunds->isNotEmpty())
                <x-admin.form-card title="Refund history">
                    <ul class="space-y-3">
                        @foreach ($order->refunds as $refund)
                            <li class="rounded-[var(--radius-admin)] border admin-border p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <a href="{{ route('admin.refunds.show', $refund) }}" class="text-sm font-medium admin-text hover:text-admin-brand">
                                        {{ MoneyFormatter::format($refund->amount_cents) }}
                                    </a>
                                    <x-admin.badge :variant="$refund->status->badgeVariant()" dot>{{ $refund->status->label() }}</x-admin.badge>
                                </div>
                                <p class="mt-1 text-xs admin-muted">{{ $refund->reason->label() }} · {{ $refund->processed_at?->format('M j, Y') }}</p>
                            </li>
                        @endforeach
                    </ul>
                </x-admin.form-card>
            @endif

            @if ($canRefund && auth('admin')->user()?->hasPermission('refunds.manage'))
                <x-admin.form-card title="Issue refund" description="Refund is sent back to the customer's original payment method.">
                    <form method="POST" action="{{ route('admin.orders.refunds.store', $order) }}" class="space-y-4">
                        @csrf
                        <x-admin.input
                            label="Refund amount"
                            name="refund_amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            :value="old('refund_amount', $refundableAmountValue)"
                            help="Remaining refundable balance: {{ $refundableAmount }}"
                            required
                        />
                        <x-admin.select label="Reason" name="reason" required>
                            @foreach ($refundReasons as $reason)
                                <option value="{{ $reason->value }}" @selected(old('reason') === $reason->value)>{{ $reason->label() }}</option>
                            @endforeach
                        </x-admin.select>
                        <x-admin.textarea
                            label="Internal note"
                            name="notes"
                            rows="3"
                            placeholder="Optional note for the refund record…"
                        >{{ old('notes') }}</x-admin.textarea>
                        <label class="flex items-center gap-3">
                            <input type="hidden" name="restore_stock" value="0">
                            <input
                                type="checkbox"
                                name="restore_stock"
                                value="1"
                                @checked(old('restore_stock', $order->refunded_cents === 0))
                                class="size-4 rounded border admin-border accent-admin-brand admin-focus-ring"
                            >
                            <span class="text-sm admin-text">Restore product stock</span>
                        </label>
                        <p class="text-xs admin-muted">Refunds typically appear on the customer's statement within {{ config('refunds.processing_days') }}.</p>
                        <x-admin.button type="submit" variant="secondary" class="w-full" onclick="return confirm('Process this refund? This cannot be undone.')">Process refund</x-admin.button>
                    </form>
                </x-admin.form-card>
            @endif

            <x-admin.form-card title="Admin notes">
                <ul class="space-y-4">
                    @forelse ($order->notes as $note)
                        <li class="border-b admin-border pb-4 last:border-0 last:pb-0">
                            <p class="text-sm admin-text">{{ $note->body }}</p>
                            <p class="mt-1 text-xs admin-muted">
                                {{ $note->author_name ?: 'Admin' }} · {{ $note->created_at?->diffForHumans() }}
                            </p>
                        </li>
                    @empty
                        <li class="text-sm admin-muted">No admin notes yet.</li>
                    @endforelse
                </ul>
            </x-admin.form-card>
        </div>
    </div>
</x-layouts.admin>
