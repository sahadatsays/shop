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
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json($errors->first()), type: 'error' });
            });
        </script>
    @endif

    <x-admin.page-header :title="$purchase->purchase_number" :description="'Supplier: '.($purchase->supplier?->name ?? '—')">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.purchases.print', $purchase)" target="_blank">Print</x-admin.button>
            @if ($purchase->status->isEditable() && auth('admin')->user()?->hasPermission('purchases.edit'))
                <x-admin.button size="sm" :href="route('admin.purchases.edit', $purchase)">Edit</x-admin.button>
            @endif
            @if ($purchase->status->canSubmit() && auth('admin')->user()?->hasPermission('purchases.create'))
                <form method="POST" action="{{ route('admin.purchases.submit', $purchase) }}">
                    @csrf
                    <x-admin.button type="submit" size="sm" variant="secondary">Submit</x-admin.button>
                </form>
            @endif
            @if ($purchase->status->canApprove() && auth('admin')->user()?->hasPermission('purchases.approve'))
                <form method="POST" action="{{ route('admin.purchases.approve', $purchase) }}">
                    @csrf
                    <x-admin.button type="submit" size="sm">Approve</x-admin.button>
                </form>
            @endif
            @if ($purchase->status->canCancel() && auth('admin')->user()?->hasPermission('purchases.cancel'))
                <form method="POST" action="{{ route('admin.purchases.cancel', $purchase) }}" onsubmit="return confirm('Cancel this purchase?')">
                    @csrf
                    <x-admin.button type="submit" size="sm" variant="danger-ghost">Cancel purchase</x-admin.button>
                </form>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Overview">
                <dl>
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$purchase->status->badgeVariant()" dot>{{ $purchase->status->label() }}</x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Payment status">
                        <x-admin.badge :variant="$purchase->payment_status->badgeVariant()" dot>{{ $purchase->payment_status->label() }}</x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Supplier" :value="$purchase->supplier?->name" />
                    <x-admin.detail-row label="Purchase date" :value="$purchase->purchase_date?->format('M j, Y')" />
                    <x-admin.detail-row label="Expected delivery" :value="$purchase->expected_delivery_date?->format('M j, Y') ?: '—'" />
                    <x-admin.detail-row label="Created by" :value="$purchase->creator?->name ?: '—'" />
                    <x-admin.detail-row label="Approved by" :value="$purchase->approver?->name ?: '—'" />
                    <x-admin.detail-row label="Notes" :value="$purchase->notes ?: '—'" />
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="Line items">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b admin-border text-left text-xs uppercase tracking-wider admin-muted">
                                <th class="px-3 py-2">Product</th>
                                <th class="px-3 py-2">Ordered</th>
                                <th class="px-3 py-2">Received</th>
                                <th class="px-3 py-2">Remaining</th>
                                <th class="px-3 py-2">Unit cost</th>
                                <th class="px-3 py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-admin-border/60">
                            @foreach ($purchase->items as $item)
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="font-medium admin-text">{{ $item->product_name_snapshot }}</div>
                                        <div class="text-xs admin-muted">{{ $item->sku_snapshot }}</div>
                                    </td>
                                    <td class="px-3 py-3 tabular-nums">{{ $item->quantity_ordered }}</td>
                                    <td class="px-3 py-3 tabular-nums">{{ $item->quantity_received }}</td>
                                    <td class="px-3 py-3 tabular-nums">{{ $item->quantityRemaining() }}</td>
                                    <td class="px-3 py-3 tabular-nums">{{ MoneyFormatter::format($item->unit_cost_cents) }}</td>
                                    <td class="px-3 py-3 tabular-nums">{{ MoneyFormatter::format($item->subtotal_cents) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-admin.form-card>

            @if ($purchase->status->canReceive() && auth('admin')->user()?->hasPermission('purchases.receive'))
                <x-admin.form-card title="Receive stock" description="Inventory increases only for quantities received here.">
                    <form method="POST" action="{{ route('admin.purchases.receive', $purchase) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', $idempotencyKey) }}">

                        <x-admin.select label="Warehouse" name="warehouse_id" required>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id', $defaultWarehouseId) === (string) $warehouse->id)>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </x-admin.select>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b admin-border text-left text-xs uppercase tracking-wider admin-muted">
                                        <th class="px-3 py-2">Product</th>
                                        <th class="px-3 py-2">Remaining</th>
                                        <th class="px-3 py-2">Receive qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase->items as $index => $item)
                                        <tr class="border-b admin-border/60">
                                            <td class="px-3 py-3">
                                                <input type="hidden" name="items[{{ $index }}][purchase_item_id]" value="{{ $item->id }}">
                                                {{ $item->product_name_snapshot }}
                                            </td>
                                            <td class="px-3 py-3 tabular-nums">{{ $item->quantityRemaining() }}</td>
                                            <td class="px-3 py-3">
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="{{ $item->quantityRemaining() }}"
                                                    name="items[{{ $index }}][quantity]"
                                                    value="{{ old('items.'.$index.'.quantity', $item->quantityRemaining()) }}"
                                                    class="w-24 rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-2 py-1.5"
                                                    @disabled($item->quantityRemaining() === 0)
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <x-admin.textarea label="Receiving notes" name="notes" :value="old('notes')" rows="2" />
                        <x-admin.button type="submit">Receive stock</x-admin.button>
                    </form>
                </x-admin.form-card>
            @endif

            @if ($purchase->receipts->isNotEmpty())
                <x-admin.form-card title="Receipt history">
                    <ul class="divide-y divide-admin-border/60 text-sm">
                        @foreach ($purchase->receipts as $receipt)
                            <li class="py-3">
                                <div class="font-medium admin-text">{{ $receipt->received_at?->format('M j, Y g:i A') }} · {{ $receipt->warehouse?->name }}</div>
                                <div class="text-xs admin-muted">
                                    By {{ $receipt->receiver?->name ?: '—' }} ·
                                    {{ $receipt->items->sum('quantity_received') }} units
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </x-admin.form-card>
            @endif
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Totals">
                <dl>
                    <x-admin.detail-row label="Subtotal" :value="MoneyFormatter::format($purchase->subtotal_cents)" />
                    <x-admin.detail-row label="Discount" :value="MoneyFormatter::format($purchase->discount_cents)" />
                    <x-admin.detail-row label="Shipping" :value="MoneyFormatter::format($purchase->shipping_cents)" />
                    <x-admin.detail-row label="Tax" :value="MoneyFormatter::format($purchase->tax_cents)" />
                    <x-admin.detail-row label="Grand total" :value="MoneyFormatter::format($purchase->grand_total_cents)" />
                    <x-admin.detail-row label="Paid" :value="MoneyFormatter::format($purchase->paid_cents)" />
                    <x-admin.detail-row label="Due" :value="MoneyFormatter::format($purchase->dueCents())" />
                    <x-admin.detail-row label="Qty ordered" :value="$purchase->totalQuantityOrdered()" />
                    <x-admin.detail-row label="Qty received" :value="$purchase->totalQuantityReceived()" />
                    <x-admin.detail-row label="Qty remaining" :value="$purchase->totalQuantityRemaining()" />
                </dl>
            </x-admin.form-card>
        </div>
    </div>
</x-layouts.admin>
