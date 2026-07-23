@php
    use App\Enums\ProductStatus;
    use App\Support\MoneyFormatter;

    $isTrashed = (bool) ($filters['trashed'] ?? false);
    $isFeatured = (bool) ($filters['featured'] ?? false);
    $isNewArrival = (bool) ($filters['new_arrival'] ?? false);
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Products" description="Manage your product catalog, inventory, and merchandising.">
        <x-slot:actions>
            @unless ($isTrashed)
                <x-admin.button :href="route('admin.products.create')" size="sm">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add product
                </x-admin.button>
            @endunless
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'All', 'href' => route('admin.products.index'), 'active' => ! $isTrashed && ! $isFeatured && ! $isNewArrival && empty($filters['status'])],
                    ['label' => 'Published', 'href' => route('admin.products.index', ['status' => ProductStatus::Published->value]), 'active' => ($filters['status'] ?? null) === ProductStatus::Published->value],
                    ['label' => 'Draft', 'href' => route('admin.products.index', ['status' => ProductStatus::Draft->value]), 'active' => ($filters['status'] ?? null) === ProductStatus::Draft->value],
                    ['label' => 'Featured', 'href' => route('admin.products.index', ['featured' => 1]), 'active' => $isFeatured && ! $isTrashed],
                    ['label' => 'New', 'href' => route('admin.products.index', ['new_arrival' => 1]), 'active' => $isNewArrival && ! $isTrashed],
                    ['label' => 'Trashed', 'href' => route('admin.products.index', ['trashed' => 1]), 'active' => $isTrashed],
                ]" />

                <form method="GET" action="{{ route('admin.products.index') }}" class="flex w-full gap-2 lg:w-auto">
                    @foreach (['trashed', 'featured', 'new_arrival', 'status'] as $filter)
                        @if (! empty($filters[$filter]))
                            <input type="hidden" name="{{ $filter }}" value="{{ is_bool($filters[$filter]) ? 1 : $filters[$filter] }}">
                        @endif
                    @endforeach
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search products, SKU, barcode…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <x-slot:mobile>
            <div class="space-y-3">
                @forelse ($products as $product)
                    <article class="rounded-[var(--radius-admin)] border admin-border bg-admin-bg/40 p-3">
                        <div class="flex items-start gap-3">
                            @if ($product->primaryImageUrl())
                                <img src="{{ $product->primaryImageUrl() }}" alt="" class="size-12 shrink-0 rounded-[var(--radius-admin)] object-cover ring-1 ring-admin-border">
                            @else
                                <span class="flex size-12 shrink-0 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-xs font-semibold admin-muted">
                                    {{ strtoupper(substr($product->name, 0, 2)) }}
                                </span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.products.show', $product) }}" class="block truncate font-medium admin-text hover:text-admin-brand">{{ $product->name }}</a>
                                <p class="truncate text-xs admin-muted">{{ $product->sku }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <x-admin.badge :variant="$product->status->badgeVariant()" dot>{{ $product->status->label() }}</x-admin.badge>
                                    @if ($product->is_featured)
                                        <x-admin.badge variant="brand">Featured</x-admin.badge>
                                    @endif
                                    @if ($product->is_new_arrival)
                                        <x-admin.badge variant="info">New</x-admin.badge>
                                    @endif
                                    <span class="text-xs admin-muted">{{ MoneyFormatter::format($product->price_cents) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 border-t admin-border pt-3">
                            @if ($isTrashed)
                                <form method="POST" action="{{ route('admin.products.restore', $product) }}">
                                    @csrf
                                    <x-admin.button type="submit" variant="soft" size="xs">Restore</x-admin.button>
                                </form>
                            @else
                                <x-admin.button variant="soft" size="xs" :href="route('admin.products.show', $product)">View</x-admin.button>
                                <x-admin.button variant="secondary" size="xs" :href="route('admin.products.edit', $product)">Edit</x-admin.button>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Move this product to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="xs">Delete</x-admin.button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <x-admin.empty-state
                        title="{{ $isTrashed ? 'Trash is empty' : 'No products yet' }}"
                        description="{{ $isTrashed ? 'Deleted products will appear here.' : 'Create your first product to start selling.' }}"
                        :action-label="$isTrashed ? null : 'Add product'"
                        :action-href="$isTrashed ? null : route('admin.products.create')"
                    />
                @endforelse
            </div>
        </x-slot:mobile>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Product</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">SKU</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Category</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Price</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Stock</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($products as $product)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if ($product->primaryImageUrl())
                                <img src="{{ $product->primaryImageUrl() }}" alt="" class="size-10 rounded-[var(--radius-admin)] object-cover ring-1 ring-admin-border">
                            @else
                                <span class="flex size-10 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-xs font-semibold admin-muted">
                                    {{ strtoupper(substr($product->name, 0, 2)) }}
                                </span>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('admin.products.show', $product) }}" class="block truncate font-medium admin-text hover:text-admin-brand">{{ $product->name }}</a>
                                <div class="mt-0.5 flex flex-wrap gap-1">
                                    @if ($product->is_featured)
                                        <x-admin.badge variant="brand">Featured</x-admin.badge>
                                    @endif
                                    @if ($product->is_new_arrival)
                                        <x-admin.badge variant="info">New</x-admin.badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-sm admin-text-secondary">{{ $product->sku }}</td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $product->category?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">{{ MoneyFormatter::format($product->price_cents) }}</td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">{{ $product->stock_quantity }}</td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$product->status->badgeVariant()" dot>{{ $product->status->label() }}</x-admin.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            @if ($isTrashed)
                                <form method="POST" action="{{ route('admin.products.restore', $product) }}">
                                    @csrf
                                    <x-admin.button type="submit" variant="soft" size="xs">Restore</x-admin.button>
                                </form>
                            @else
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.products.show', $product)" title="View" aria-label="View {{ $product->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </x-admin.button>
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.products.edit', $product)" title="Edit" aria-label="Edit {{ $product->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </x-admin.button>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Move this product to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="icon-sm" title="Delete" aria-label="Delete {{ $product->name }}">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                    </x-admin.button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-14">
                        <x-admin.empty-state
                            title="{{ $isTrashed ? 'Trash is empty' : 'No products yet' }}"
                            description="{{ $isTrashed ? 'Deleted products will appear here.' : 'Create your first product to start selling.' }}"
                            :action-label="$isTrashed ? null : 'Add product'"
                            :action-href="$isTrashed ? null : route('admin.products.create')"
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
