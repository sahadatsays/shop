@php
    $stockStatus = $filters['stock_status'] ?? null;
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Inventory" description="Track stock levels, adjustments, and warehouse availability.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.inventory.movements')">Stock history</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Total products" :value="(string) $summary['total']" />
        <x-admin.stat-card label="In stock" :value="(string) $summary['in_stock']" />
        <x-admin.stat-card label="Low stock" :value="(string) $summary['low_stock']" />
        <x-admin.stat-card label="Out of stock" :value="(string) $summary['out_of_stock']" />
    </div>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'All', 'href' => route('admin.inventory.index'), 'active' => ! $stockStatus && empty($filters['warehouse_id'])],
                    ['label' => 'In stock', 'href' => route('admin.inventory.index', ['stock_status' => 'in_stock']), 'active' => $stockStatus === 'in_stock'],
                    ['label' => 'Low stock', 'href' => route('admin.inventory.index', ['stock_status' => 'low_stock']), 'active' => $stockStatus === 'low_stock'],
                    ['label' => 'Out of stock', 'href' => route('admin.inventory.index', ['stock_status' => 'out_of_stock']), 'active' => $stockStatus === 'out_of_stock'],
                ]" />

                <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
                    @if ($stockStatus)
                        <input type="hidden" name="stock_status" value="{{ $stockStatus }}">
                    @endif
                    <select name="warehouse_id" class="rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 py-2.5 text-sm admin-text admin-focus-ring">
                        <option value="">All warehouses</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected((int) ($filters['warehouse_id'] ?? 0) === $warehouse->id)>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search products or SKU…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Filter</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Product</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">SKU</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">On hand</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Threshold</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($products as $product)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.inventory.show', $product) }}" class="font-medium admin-text hover:text-admin-brand">{{ $product->name }}</a>
                        <p class="text-xs admin-muted">{{ $product->category?->name }}</p>
                    </td>
                    <td class="px-6 py-4 font-mono text-sm admin-text-secondary">{{ $product->sku }}</td>
                    <td class="px-6 py-4 text-sm tabular-nums font-medium admin-text">{{ $product->stock_quantity }}</td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">{{ $product->low_stock_threshold }}</td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$product->stockStatusBadgeVariant()" dot>{{ $product->stockStatusLabel() }}</x-admin.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.inventory.show', $product)" title="View" aria-label="View {{ $product->name }}">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </x-admin.button>
                            <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.inventory.adjust', $product)" title="Adjust stock" aria-label="Adjust stock for {{ $product->name }}">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                            </x-admin.button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-14">
                        <x-admin.empty-state
                            title="No inventory records"
                            description="Published products with stock tracking will appear here."
                            action-label="View products"
                            :action-href="route('admin.products.index')"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            <x-admin.pagination :paginator="$products" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
