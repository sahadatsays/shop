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

    <x-admin.page-header :title="$supplier->name" description="Supplier profile and purchasing relationship summary.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('suppliers.edit'))
                <x-admin.button size="sm" :href="route('admin.suppliers.edit', $supplier)">Edit supplier</x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Supplier information">
                <dl>
                    <x-admin.detail-row label="Supplier name" :value="$supplier->name" />
                    <x-admin.detail-row label="Company name" :value="$supplier->company_name" />
                    <x-admin.detail-row label="Contact person" :value="$supplier->contact_person" />
                    <x-admin.detail-row label="Phone" :value="$supplier->phone" />
                    <x-admin.detail-row label="Email" :value="$supplier->email" />
                    <x-admin.detail-row label="Address" :value="$supplier->address" />
                    <x-admin.detail-row label="City" :value="$supplier->city" />
                    <x-admin.detail-row label="District" :value="$supplier->district" />
                    <x-admin.detail-row label="Country" :value="$supplier->country" />
                    <x-admin.detail-row label="Tax / Business ID" :value="$supplier->tax_id" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$supplier->status->badgeVariant()" dot>{{ $supplier->status->label() }}</x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Notes" :value="$supplier->notes" />
                    <x-admin.detail-row label="Created" :value="$supplier->created_at?->format('M j, Y g:i A')" />
                </dl>
            </x-admin.form-card>

            @if ($canViewPurchases)
                <x-admin.form-card title="Purchase history" description="Existing history stays linked if the supplier is inactivated.">
                    @if ($purchaseHistory->isEmpty())
                        <x-admin.empty-state
                            title="No purchases yet"
                            description="Purchases linked to this supplier will appear here."
                        />
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b admin-border text-left text-xs uppercase tracking-wider admin-muted">
                                        <th class="px-2 py-2">Purchase</th>
                                        <th class="px-2 py-2">Date</th>
                                        <th class="px-2 py-2">Total</th>
                                        <th class="px-2 py-2">Received</th>
                                        <th class="px-2 py-2">Status</th>
                                        <th class="px-2 py-2">Payment</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-admin-border/60">
                                    @foreach ($purchaseHistory as $purchase)
                                        <tr>
                                            <td class="px-2 py-3">
                                                @if (auth('admin')->user()?->hasPermission('purchases.view'))
                                                    <a href="{{ route('admin.purchases.show', $purchase) }}" class="font-medium hover:text-admin-brand">{{ $purchase->purchase_number }}</a>
                                                @else
                                                    {{ $purchase->purchase_number }}
                                                @endif
                                            </td>
                                            <td class="px-2 py-3">{{ $purchase->purchase_date?->format('M j, Y') }}</td>
                                            <td class="px-2 py-3 tabular-nums">{{ MoneyFormatter::format($purchase->grand_total_cents) }}</td>
                                            <td class="px-2 py-3 tabular-nums">{{ (int) ($purchase->quantity_received_sum ?? 0) }} / {{ (int) ($purchase->quantity_ordered_sum ?? 0) }}</td>
                                            <td class="px-2 py-3"><x-admin.badge :variant="$purchase->status->badgeVariant()" dot>{{ $purchase->status->label() }}</x-admin.badge></td>
                                            <td class="px-2 py-3"><x-admin.badge :variant="$purchase->payment_status->badgeVariant()" dot>{{ $purchase->payment_status->label() }}</x-admin.badge></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-admin.form-card>

                <x-admin.form-card title="Products purchased" description="Distinct products sourced from this supplier.">
                    @if (($purchaseSummary?->productsPurchased ?? []) === [])
                        <p class="text-sm admin-muted">No products purchased yet.</p>
                    @else
                        <ul class="divide-y divide-admin-border/60">
                            @foreach ($purchaseSummary->productsPurchased as $product)
                                <li class="flex items-center justify-between gap-3 py-3 text-sm">
                                    <span class="admin-text">{{ $product['name'] }}</span>
                                    <span class="tabular-nums admin-muted">{{ $product['quantity'] }} · {{ MoneyFormatter::format((int) $product['total_cents']) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-admin.form-card>
            @endif
        </div>

        <div class="space-y-6">
            @if ($canViewPurchases && $purchaseSummary)
                <x-admin.form-card title="Purchasing summary">
                    <dl>
                        <x-admin.detail-row label="Purchase count" :value="$purchaseSummary->purchaseCount" />
                        <x-admin.detail-row label="Total purchase value" :value="MoneyFormatter::format($purchaseSummary->totalPurchaseValueCents)" />
                        <x-admin.detail-row label="Outstanding payable" :value="MoneyFormatter::format($purchaseSummary->outstandingPayableCents)" />
                        <x-admin.detail-row label="Last purchase" :value="$purchaseSummary->lastPurchaseAt ?: '—'" />
                    </dl>
                </x-admin.form-card>
            @endif

            @if (auth('admin')->user()?->hasPermission('suppliers.delete'))
                <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete this supplier? Suppliers with purchase history cannot be deleted.')">
                    @csrf
                    @method('DELETE')
                    <x-admin.button type="submit" variant="danger-ghost" class="w-full">Delete supplier</x-admin.button>
                </form>
            @endif
        </div>
    </div>
</x-layouts.admin>
