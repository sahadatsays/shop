@php
    $isTrashed = (bool) ($filters['trashed'] ?? false);
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Categories" description="Organize products into categories and subcategories.">
        <x-slot:actions>
            @unless ($isTrashed)
                <x-admin.button :href="route('admin.categories.create')" size="sm">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add category
                </x-admin.button>
            @endunless
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'Active', 'href' => route('admin.categories.index'), 'active' => ! $isTrashed],
                    ['label' => 'Trashed', 'href' => route('admin.categories.index', ['trashed' => 1]), 'active' => $isTrashed],
                ]" />

                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex w-full gap-2 lg:w-auto">
                    @if ($isTrashed)
                        <input type="hidden" name="trashed" value="1">
                    @endif
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search categories…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <x-slot:mobile>
            <div class="space-y-3">
                @forelse ($categories as $category)
                    <article class="rounded-[var(--radius-admin)] border admin-border bg-admin-bg/40 p-3">
                        <div class="flex items-start gap-3">
                            @if ($category->imageUrl())
                                <img src="{{ $category->imageUrl() }}" alt="" class="size-12 shrink-0 rounded-[var(--radius-admin)] object-cover">
                            @else
                                <span class="flex size-12 shrink-0 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-xs font-semibold admin-muted">
                                    {{ strtoupper(substr($category->name, 0, 2)) }}
                                </span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.categories.show', $category) }}" class="block truncate font-medium admin-text hover:underline">{{ $category->name }}</a>
                                <p class="truncate text-xs admin-muted">{{ $category->slug }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <x-admin.badge :variant="$category->status === \App\Enums\CategoryStatus::Active ? 'success' : 'muted'" dot>
                                        {{ $category->status->label() }}
                                    </x-admin.badge>
                                    <span class="text-xs admin-muted">{{ $category->products_count }} products</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 border-t admin-border pt-3">
                            @if ($isTrashed)
                                <form method="POST" action="{{ route('admin.categories.restore', $category) }}">
                                    @csrf
                                    <x-admin.button type="submit" variant="soft" size="xs">Restore</x-admin.button>
                                </form>
                            @else
                                <x-admin.button variant="soft" size="xs" :href="route('admin.categories.show', $category)">View</x-admin.button>
                                <x-admin.button variant="secondary" size="xs" :href="route('admin.categories.edit', $category)">Edit</x-admin.button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Move this category to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="xs">Delete</x-admin.button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <x-admin.empty-state
                        title="{{ $isTrashed ? 'Trash is empty' : 'No categories yet' }}"
                        description="{{ $isTrashed ? 'Deleted categories will appear here.' : 'Create your first category to organize products.' }}"
                        :action-label="$isTrashed ? null : 'Add category'"
                        :action-href="$isTrashed ? null : route('admin.categories.create')"
                    />
                @endforelse
            </div>
        </x-slot:mobile>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Category</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Parent</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Products</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Sort</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($categories as $category)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if ($category->imageUrl())
                                <img src="{{ $category->imageUrl() }}" alt="" class="size-10 rounded-[var(--radius-admin)] object-cover ring-1 ring-admin-border">
                            @else
                                <span class="flex size-10 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-xs font-semibold admin-muted">
                                    {{ strtoupper(substr($category->name, 0, 2)) }}
                                </span>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('admin.categories.show', $category) }}" class="block truncate font-medium admin-text hover:text-admin-brand">{{ $category->name }}</a>
                                <p class="truncate text-xs admin-muted">{{ $category->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $category->parent?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">{{ $category->products_count }}</td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$category->status === \App\Enums\CategoryStatus::Active ? 'success' : 'muted'" dot>
                            {{ $category->status->label() }}
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">{{ $category->sort_order }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            @if ($isTrashed)
                                <form method="POST" action="{{ route('admin.categories.restore', $category) }}">
                                    @csrf
                                    <x-admin.button type="submit" variant="soft" size="xs">Restore</x-admin.button>
                                </form>
                            @else
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.categories.show', $category)" title="View" aria-label="View {{ $category->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </x-admin.button>
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.categories.edit', $category)" title="Edit" aria-label="Edit {{ $category->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </x-admin.button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Move this category to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="icon-sm" title="Delete" aria-label="Delete {{ $category->name }}">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                    </x-admin.button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-14">
                        <x-admin.empty-state
                            title="{{ $isTrashed ? 'Trash is empty' : 'No categories yet' }}"
                            description="{{ $isTrashed ? 'Deleted categories will appear here.' : 'Create your first category to organize products.' }}"
                            :action-label="$isTrashed ? null : 'Add category'"
                            :action-href="$isTrashed ? null : route('admin.categories.create')"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            <x-admin.pagination :paginator="$categories" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
