@php
    $isFeatured = ($filters['featured'] ?? null) === '1';
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Collections" description="Curated product groups for homepage and campaign placements.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('collections.manage'))
                <x-admin.button :href="route('admin.collections.create')" size="sm">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add collection
                </x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'All', 'href' => route('admin.collections.index'), 'active' => ! $isFeatured],
                    ['label' => 'Featured', 'href' => route('admin.collections.index', ['featured' => 1]), 'active' => $isFeatured],
                ]" />

                <form method="GET" action="{{ route('admin.collections.index') }}" class="flex w-full gap-2 lg:w-auto">
                    @if ($isFeatured)
                        <input type="hidden" name="featured" value="1">
                    @endif
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search collections…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Collection</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Products</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Featured</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($collections as $collection)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if ($collection->imageUrl())
                                <img src="{{ $collection->imageUrl() }}" alt="" class="size-10 rounded-[var(--radius-admin)] object-cover ring-1 ring-admin-border">
                            @else
                                <span class="flex size-10 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-xs font-semibold admin-muted">
                                    {{ strtoupper(substr($collection->name, 0, 2)) }}
                                </span>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('admin.collections.show', $collection) }}" class="block truncate font-medium admin-text hover:text-admin-brand">{{ $collection->name }}</a>
                                <p class="truncate text-xs admin-muted">{{ $collection->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">{{ $collection->products_count }}</td>
                    <td class="px-6 py-4">
                        @if ($collection->is_featured)
                            <x-admin.badge variant="brand">Featured</x-admin.badge>
                        @else
                            <span class="text-sm admin-muted">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$collection->is_active ? 'success' : 'muted'" dot>
                            {{ $collection->is_active ? 'Active' : 'Inactive' }}
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.collections.show', $collection)" title="View" aria-label="View {{ $collection->name }}">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </x-admin.button>
                            @if (auth('admin')->user()?->hasPermission('collections.manage'))
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.collections.edit', $collection)" title="Edit" aria-label="Edit {{ $collection->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </x-admin.button>
                                <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}" onsubmit="return confirm('Delete this collection?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="icon-sm" title="Delete" aria-label="Delete {{ $collection->name }}">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                    </x-admin.button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-14">
                        <x-admin.empty-state
                            title="No collections yet"
                            description="Group products into curated collections for the storefront."
                            action-label="Add collection"
                            :action-href="route('admin.collections.create')"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            <x-admin.pagination :paginator="$collections" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
