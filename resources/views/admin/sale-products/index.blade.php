@php
    $onSale = ($filters['on_sale'] ?? '1') === '1';
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Sale Products" description="Manage sale pricing with inline price updates.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.products.index')">All products</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'On sale', 'href' => route('admin.sale-products.index', ['on_sale' => 1]), 'active' => $onSale],
                    ['label' => 'All published', 'href' => route('admin.sale-products.index', ['on_sale' => 0]), 'active' => ! $onSale],
                ]" />

                <form method="GET" action="{{ route('admin.sale-products.index') }}" class="flex w-full gap-2 lg:w-auto">
                    <input type="hidden" name="on_sale" value="{{ $onSale ? '1' : '0' }}">
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search name or SKU…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Product</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Current</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Update pricing</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($products as $product)
                @php
                    $price = number_format($product->price_cents / 100, 2, '.', '');
                    $compareAt = $product->compare_at_price_cents
                        ? number_format($product->compare_at_price_cents / 100, 2, '.', '')
                        : '';
                @endphp
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.products.show', $product) }}" class="font-medium admin-text hover:text-admin-brand">{{ $product->name }}</a>
                        <p class="text-xs admin-muted">{{ $product->sku }} · {{ $product->category?->name }}</p>
                        @if ($product->isOnSale())
                            <x-admin.badge variant="brand" class="mt-2">{{ $product->discountPercent() }}% off</x-admin.badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $product->formattedPrice() }}</td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.sale-products.update', $product) }}" class="flex flex-wrap items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input
                                type="number"
                                name="price"
                                step="0.01"
                                min="0"
                                value="{{ $price }}"
                                aria-label="Sale price for {{ $product->name }}"
                                class="w-28 rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3 py-2 text-sm admin-text admin-focus-ring"
                            >
                            <input
                                type="number"
                                name="compare_at_price"
                                step="0.01"
                                min="0"
                                value="{{ $compareAt }}"
                                placeholder="Compare-at"
                                aria-label="Compare-at price for {{ $product->name }}"
                                class="w-28 rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3 py-2 text-sm admin-text admin-focus-ring"
                            >
                            @if (auth('admin')->user()?->hasPermission('sale-products.manage'))
                                <x-admin.button type="submit" variant="soft" size="xs">Save</x-admin.button>
                            @endif
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-5 py-14">
                        <x-admin.empty-state
                            title="{{ $onSale ? 'No sale products' : 'No products found' }}"
                            description="{{ $onSale ? 'Products with compare-at pricing appear here.' : 'Try adjusting your search or filters.' }}"
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
