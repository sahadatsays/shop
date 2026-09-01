@php
    use App\Enums\RefundStatus;
    use App\Support\MoneyFormatter;

    $filter = $filters['filter'] ?? null;
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

    <x-admin.page-header title="Refunds" description="Process returns, issue refunds, and track payment reversals.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.orders.index')">View orders</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-admin.stat-card label="Pending returns" :value="(string) $summary['pending_returns']" />
        <x-admin.stat-card label="Refunded today" :value="(string) $summary['completed_today']" />
        <x-admin.stat-card label="Total refunded" :value="MoneyFormatter::format($summary['refunded_cents'])" />
    </div>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'All', 'href' => route('admin.refunds.index'), 'active' => ! $filter && ! $status],
                    ['label' => 'Pending returns', 'href' => route('admin.refunds.index', ['filter' => 'pending_returns']), 'active' => $filter === 'pending_returns'],
                    ['label' => 'Completed', 'href' => route('admin.refunds.index', ['filter' => 'completed']), 'active' => $filter === 'completed'],
                ]" />

                <form method="GET" action="{{ route('admin.refunds.index') }}" class="flex w-full gap-2 lg:w-auto">
                    @if ($filter)
                        <input type="hidden" name="filter" value="{{ $filter }}">
                    @endif
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search order, customer, reference…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Refund</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Order</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Customer</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Amount</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @if ($filter === 'pending_returns' && $pendingReturnOrders)
                @forelse ($pendingReturnOrders as $pendingOrder)
                    <tr class="transition-colors hover:bg-admin-bg/60">
                        <td class="px-6 py-4 text-sm admin-text-secondary">—</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $pendingOrder) }}" class="font-medium admin-text hover:text-admin-brand">{{ $pendingOrder->order_number }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm admin-text-secondary">{{ $pendingOrder->customer?->name }}</td>
                        <td class="px-6 py-4 text-sm font-medium tabular-nums admin-text">{{ MoneyFormatter::format($pendingOrder->refundableCents()) }}</td>
                        <td class="px-6 py-4">
                            <x-admin.badge variant="warning" dot>Return requested</x-admin.badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <x-admin.button variant="ghost" size="sm" :href="route('admin.orders.show', $pendingOrder)">Process</x-admin.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-14">
                            <x-admin.empty-state
                                title="No pending returns"
                                description="Customer return requests will appear here for refund processing."
                            />
                        </td>
                    </tr>
                @endforelse
            @else
            @forelse ($refunds as $refund)
                <tr class="transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4 text-sm admin-text-secondary">#{{ $refund->id }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.orders.show', $refund->order) }}" class="font-medium admin-text hover:text-admin-brand">{{ $refund->order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $refund->order->customer?->name }}</td>
                    <td class="px-6 py-4 text-sm font-medium tabular-nums admin-text">{{ MoneyFormatter::format($refund->amount_cents) }}</td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$refund->status->badgeVariant()" dot>{{ $refund->status->label() }}</x-admin.badge>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <x-admin.button variant="ghost" size="sm" :href="route('admin.refunds.show', $refund)">View</x-admin.button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-14">
                        <x-admin.empty-state
                            title="No refunds yet"
                            description="Refunds will appear here after you process them from an order."
                            action-label="View orders"
                            :action-href="route('admin.orders.index')"
                        />
                    </td>
                </tr>
            @endforelse
            @endif
        </tbody>

        <x-slot:footer>
            @if ($filter === 'pending_returns' && $pendingReturnOrders)
                <x-admin.pagination :paginator="$pendingReturnOrders" />
            @else
                <x-admin.pagination :paginator="$refunds" />
            @endif
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
