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

    <x-admin.page-header :title="'Refund #'.$refund->id" description="Refund details and payment confirmation.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.orders.show', $refund->order)">View order</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Refund details">
                <dl>
                    <x-admin.detail-row label="Amount" :value="MoneyFormatter::format($refund->amount_cents)" />
                    <x-admin.detail-row label="Reason" :value="$refund->reason->label()" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$refund->status->badgeVariant()" dot>{{ $refund->status->label() }}</x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Payment reference">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $refund->payment_reference ?? '—' }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Restore stock" :value="$refund->restore_stock ? 'Yes' : 'No'" />
                    <x-admin.detail-row label="Processed by" :value="$refund->processedBy?->name ?? 'System'" />
                    <x-admin.detail-row label="Processed at" :value="$refund->processed_at?->format('M j, Y g:i A') ?? '—'" />
                    <x-admin.detail-row label="Notes" :value="$refund->notes" />
                </dl>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Order">
                <dl>
                    <x-admin.detail-row label="Order number">
                        <a href="{{ route('admin.orders.show', $refund->order) }}" class="text-sm font-medium admin-text hover:text-admin-brand">{{ $refund->order->order_number }}</a>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Customer" :value="$refund->order->customer?->name" />
                    <x-admin.detail-row label="Payment method" :value="$refund->order->payment_method" />
                    <x-admin.detail-row label="Order total" :value="MoneyFormatter::format($refund->order->total_cents)" />
                    <x-admin.detail-row label="Total refunded" :value="MoneyFormatter::format($refund->order->refunded_cents)" />
                </dl>
            </x-admin.form-card>
        </div>
    </div>
</x-layouts.admin>
