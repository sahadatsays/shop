<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Brands" description="Manage product brands, logos, and featured placement.">
        <x-slot:actions>
            @unless($filters['trashed'] ?? false)
                <x-admin.button :href="route('admin.brands.create')" size="sm">Add brand</x-admin.button>
            @endunless
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.brands.index') }}"
               @class([
                   'rounded-[var(--radius-admin)] px-3 py-1.5 text-sm font-medium admin-focus-ring',
                   'bg-admin-accent-muted admin-text' => ! ($filters['trashed'] ?? false) && ! ($filters['featured'] ?? false),
                   'admin-text-secondary hover:bg-admin-accent-muted/60' => ($filters['trashed'] ?? false) || ($filters['featured'] ?? false),
               ])>
                All
            </a>
            <a href="{{ route('admin.brands.index', ['featured' => 1]) }}"
               @class([
                   'rounded-[var(--radius-admin)] px-3 py-1.5 text-sm font-medium admin-focus-ring',
                   'bg-admin-accent-muted admin-text' => ($filters['featured'] ?? false) && ! ($filters['trashed'] ?? false),
                   'admin-text-secondary hover:bg-admin-accent-muted/60' => ! (($filters['featured'] ?? false) && ! ($filters['trashed'] ?? false)),
               ])>
                Featured
            </a>
            <a href="{{ route('admin.brands.index', ['trashed' => 1]) }}"
               @class([
                   'rounded-[var(--radius-admin)] px-3 py-1.5 text-sm font-medium admin-focus-ring',
                   'bg-admin-accent-muted admin-text' => $filters['trashed'] ?? false,
                   'admin-text-secondary hover:bg-admin-accent-muted/60' => ! ($filters['trashed'] ?? false),
               ])>
                Trashed
            </a>
        </div>
        <form method="GET" action="{{ route('admin.brands.index') }}" class="flex gap-2">
            @if ($filters['trashed'] ?? false)
                <input type="hidden" name="trashed" value="1">
            @endif
            @if ($filters['featured'] ?? false)
                <input type="hidden" name="featured" value="1">
            @endif
            <x-admin.input name="search" placeholder="Search brands…" :value="$filters['search'] ?? ''" class="min-w-[12rem]" />
            <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
        </form>
    </div>

    <x-admin.data-table>
        <thead class="border-b admin-border bg-admin-bg/60 text-xs uppercase tracking-wide admin-muted">
            <tr>
                <th scope="col" class="px-4 py-3 font-medium">Brand</th>
                <th scope="col" class="px-4 py-3 font-medium">Products</th>
                <th scope="col" class="hidden px-4 py-3 font-medium sm:table-cell">Status</th>
                <th scope="col" class="hidden px-4 py-3 font-medium md:table-cell">Featured</th>
                <th scope="col" class="hidden px-4 py-3 font-medium lg:table-cell">Sort</th>
                <th scope="col" class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y admin-border">
            @forelse ($brands as $brand)
                <tr class="admin-table-row hover:bg-admin-accent-muted/30">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if ($brand->logoUrl())
                                <img src="{{ $brand->logoUrl() }}" alt="" class="size-10 rounded-[var(--radius-admin)] object-contain bg-admin-bg p-1">
                            @else
                                <span class="flex size-10 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-xs font-semibold admin-muted">
                                    {{ strtoupper(substr($brand->name, 0, 2)) }}
                                </span>
                            @endif
                            <div class="min-w-0">
                                <p class="truncate font-medium admin-text">{{ $brand->name }}</p>
                                <p class="truncate text-xs admin-muted">{{ $brand->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 admin-text-secondary">{{ $brand->products_count }}</td>
                    <td class="hidden px-4 py-3 sm:table-cell">
                        <x-admin.badge :variant="$brand->status === \App\Enums\BrandStatus::Active ? 'success' : 'muted'">
                            {{ $brand->status->label() }}
                        </x-admin.badge>
                    </td>
                    <td class="hidden px-4 py-3 md:table-cell">
                        @if ($brand->is_featured)
                            <x-admin.badge variant="brand">Featured</x-admin.badge>
                        @else
                            <span class="admin-muted">—</span>
                        @endif
                    </td>
                    <td class="hidden px-4 py-3 admin-text-secondary lg:table-cell">{{ $brand->sort_order }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            @if ($filters['trashed'] ?? false)
                                <form method="POST" action="{{ route('admin.brands.restore', $brand) }}">
                                    @csrf
                                    <x-admin.button type="submit" variant="ghost" size="sm">Restore</x-admin.button>
                                </form>
                            @else
                                <x-admin.button variant="ghost" size="sm" :href="route('admin.brands.edit', $brand)">Edit</x-admin.button>
                                <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Move this brand to trash?')">
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
                            title="{{ ($filters['trashed'] ?? false) ? 'Trash is empty' : 'No brands yet' }}"
                            description="{{ ($filters['trashed'] ?? false) ? 'Deleted brands will appear here.' : 'Create your first brand to organize products.' }}"
                            :action-label="($filters['trashed'] ?? false) ? null : 'Add brand'"
                            :action-href="($filters['trashed'] ?? false) ? null : route('admin.brands.create')"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>
        <x-slot:footer>
            <x-admin.pagination :paginator="$brands" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
