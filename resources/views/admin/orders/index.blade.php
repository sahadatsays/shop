@php
    use App\Enums\OrderStatus;
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

    <x-admin.page-header title="Orders" description="Manage order lifecycle, fulfillment status, and internal notes.">
    </x-admin.page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Total orders" :value="(string) $summary['total']" />
        <x-admin.stat-card label="Open orders" :value="(string) $summary['pending']" />
        <x-admin.stat-card label="In transit" :value="(string) $summary['shipped']" />
        <x-admin.stat-card label="Lifetime revenue" :value="MoneyFormatter::format($summary['revenue_cents'])" />
    </div>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'All', 'href' => route('admin.orders.index'), 'active' => ! $status],
                    ['label' => 'Pending', 'href' => route('admin.orders.index', ['status' => OrderStatus::Pending->value]), 'active' => $status === OrderStatus::Pending->value],
                    ['label' => 'Processing', 'href' => route('admin.orders.index', ['status' => OrderStatus::Processing->value]), 'active' => $status === OrderStatus::Processing->value],
                    ['label' => 'Shipped', 'href' => route('admin.orders.index', ['status' => OrderStatus::Shipped->value]), 'active' => $status === OrderStatus::Shipped->value],
                    ['label' => 'Delivered', 'href' => route('admin.orders.index', ['status' => OrderStatus::Delivered->value]), 'active' => $status === OrderStatus::Delivered->value],
                    ['label' => 'Cancelled', 'href' => route('admin.orders.index', ['status' => OrderStatus::Cancelled->value]), 'active' => $status === OrderStatus::Cancelled->value],
                ]" />

                <form method="GET" action="{{ route('admin.orders.index') }}" class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
                    @if ($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <select name="status" class="rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 py-2.5 text-sm admin-text admin-focus-ring">
                        <option value="">Any status</option>
                        @foreach (OrderStatus::cases() as $orderStatus)
                            <option value="{{ $orderStatus->value }}" @selected(($filters['status'] ?? null) === $orderStatus->value)>
                                {{ $orderStatus->label() }}
                            </option>
                        @endforeach
                    </select>
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search order # or customer…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Order</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Customer</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Placed</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Total</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($orders as $order)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.orders.show', $order) }}" class="font-medium admin-text hover:text-admin-brand">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.customers.show', $order->customer) }}" class="admin-text hover:text-admin-brand">{{ $order->customer->name }}</a>
                        <p class="text-xs admin-muted">{{ $order->customer->email }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $order->placed_at?->format('M j, Y g:i A') }}</td>
                    <td class="px-6 py-4 text-sm tabular-nums font-medium admin-text">{{ MoneyFormatter::format($order->total_cents) }}</td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$order->status->badgeVariant()" dot>{{ $order->status->label() }}</x-admin.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end">
                            <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.orders.show', $order)" title="View order" aria-label="View order {{ $order->order_number }}">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </x-admin.button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-14">
                        <x-admin.empty-state title="No orders found" description="Try adjusting your search or status filters." />
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if ($orders->hasPages())
            <x-slot:footer>
                {{ $orders->links() }}
            </x-slot:footer>
        @endif
    </x-admin.data-table>
</x-layouts.admin>
