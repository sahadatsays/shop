@php
    use App\Enums\PurchaseStatus;
    use App\Support\MoneyFormatter;

    $status = $filters['status'] ?? null;
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Purchases" description="Purchase products from suppliers and receive stock into inventory.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('purchases.create'))
                <x-admin.button :href="route('admin.purchases.create')" size="sm">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Create purchase
                </x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'All', 'href' => route('admin.purchases.index'), 'active' => ! $status],
                    ['label' => 'Draft', 'href' => route('admin.purchases.index', ['status' => PurchaseStatus::Draft->value]), 'active' => $status === PurchaseStatus::Draft->value],
                    ['label' => 'Approved', 'href' => route('admin.purchases.index', ['status' => PurchaseStatus::Approved->value]), 'active' => $status === PurchaseStatus::Approved->value],
                    ['label' => 'Receiving', 'href' => route('admin.purchases.index', ['status' => PurchaseStatus::PartiallyReceived->value]), 'active' => $status === PurchaseStatus::PartiallyReceived->value],
                    ['label' => 'Completed', 'href' => route('admin.purchases.index', ['status' => PurchaseStatus::Completed->value]), 'active' => $status === PurchaseStatus::Completed->value],
                ]" />

                <form method="GET" action="{{ route('admin.purchases.index') }}" class="flex w-full gap-2 lg:w-auto">
                    @if ($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search purchase # or supplier…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Purchase</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Supplier</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Date</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Received</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Total</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($purchases as $purchase)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.purchases.show', $purchase) }}" class="font-medium admin-text hover:text-admin-brand">{{ $purchase->purchase_number }}</a>
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $purchase->supplier?->name }}</td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $purchase->purchase_date?->format('M j, Y') }}</td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">
                        {{ (int) ($purchase->quantity_received_sum ?? 0) }} / {{ (int) ($purchase->quantity_ordered_sum ?? 0) }}
                    </td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text">{{ MoneyFormatter::format($purchase->grand_total_cents) }}</td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$purchase->status->badgeVariant()" dot>{{ $purchase->status->label() }}</x-admin.badge>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.purchases.show', $purchase)" title="View">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </x-admin.button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-14">
                        <x-admin.empty-state
                            title="No purchases yet"
                            description="Create a purchase order to buy stock from suppliers."
                            :action-label="auth('admin')->user()?->hasPermission('purchases.create') ? 'Create purchase' : null"
                            :action-href="auth('admin')->user()?->hasPermission('purchases.create') ? route('admin.purchases.create') : null"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            <x-admin.pagination :paginator="$purchases" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
