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
            @unless($filters['trashed'] ?? false)
                <x-admin.button :href="route('admin.categories.create')" size="sm">Add category</x-admin.button>
            @endunless
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Filters --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.categories.index') }}"
               @class([
                   'rounded-[var(--radius-admin)] px-3 py-1.5 text-sm font-medium admin-focus-ring',
                   'bg-admin-accent-muted admin-text' => ! ($filters['trashed'] ?? false),
                   'admin-text-secondary hover:bg-admin-accent-muted/60' => $filters['trashed'] ?? false,
               ])>
                Active
            </a>
            <a href="{{ route('admin.categories.index', ['trashed' => 1]) }}"
               @class([
                   'rounded-[var(--radius-admin)] px-3 py-1.5 text-sm font-medium admin-focus-ring',
                   'bg-admin-accent-muted admin-text' => $filters['trashed'] ?? false,
                   'admin-text-secondary hover:bg-admin-accent-muted/60' => ! ($filters['trashed'] ?? false),
               ])>
                Trashed
            </a>
        </div>
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex gap-2">
            @if ($filters['trashed'] ?? false)
                <input type="hidden" name="trashed" value="1">
            @endif
            <x-admin.input name="search" placeholder="Search categories…" :value="$filters['search'] ?? ''" class="min-w-[12rem]" />
            <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
        </form>
    </div>

    <x-admin.data-table>
        <thead class="border-b admin-border bg-admin-bg/60 text-xs uppercase tracking-wide admin-muted">
            <tr>
                <th scope="col" class="px-4 py-3 font-medium">Category</th>
                <th scope="col" class="hidden px-4 py-3 font-medium md:table-cell">Parent</th>
                <th scope="col" class="px-4 py-3 font-medium">Products</th>
                <th scope="col" class="hidden px-4 py-3 font-medium sm:table-cell">Status</th>
                <th scope="col" class="hidden px-4 py-3 font-medium lg:table-cell">Sort</th>
                <th scope="col" class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y admin-border">
            @forelse ($categories as $category)
                <tr class="admin-table-row hover:bg-admin-accent-muted/30">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if ($category->imageUrl())
                                <img src="{{ $category->imageUrl() }}" alt="" class="size-10 rounded-[var(--radius-admin)] object-cover">
                            @else
                                <span class="flex size-10 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-xs font-semibold admin-muted">
                                    {{ strtoupper(substr($category->name, 0, 2)) }}
                                </span>
                            @endif
                            <div class="min-w-0">
                                <p class="truncate font-medium admin-text">{{ $category->name }}</p>
                                <p class="truncate text-xs admin-muted">{{ $category->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="hidden px-4 py-3 admin-text-secondary md:table-cell">
                        {{ $category->parent?->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 admin-text-secondary">{{ $category->products_count }}</td>
                    <td class="hidden px-4 py-3 sm:table-cell">
                        <x-admin.badge :variant="$category->status === \App\Enums\CategoryStatus::Active ? 'success' : 'muted'">
                            {{ $category->status->label() }}
                        </x-admin.badge>
                    </td>
                    <td class="hidden px-4 py-3 admin-text-secondary lg:table-cell">{{ $category->sort_order }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            @if ($filters['trashed'] ?? false)
                                <form method="POST" action="{{ route('admin.categories.restore', $category->id) }}">
                                    @csrf
                                    <x-admin.button type="submit" variant="ghost" size="sm">Restore</x-admin.button>
                                </form>
                            @else
                                <x-admin.button variant="ghost" size="sm" :href="route('admin.categories.edit', $category)">Edit</x-admin.button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Move this category to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="ghost" size="sm">Delete</x-admin.button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12">
                        <x-admin.empty-state
                            title="{{ ($filters['trashed'] ?? false) ? 'Trash is empty' : 'No categories yet' }}"
                            description="{{ ($filters['trashed'] ?? false) ? 'Deleted categories will appear here.' : 'Create your first category to organize products.' }}"
                            :action-label="($filters['trashed'] ?? false) ? null : 'Add category'"
                            :action-href="($filters['trashed'] ?? false) ? null : route('admin.categories.create')"
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
