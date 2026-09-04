@php
    $status = $filters['is_active'] ?? null;
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Warehouses" description="Manage fulfillment locations used for inventory tracking.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('warehouses.manage'))
                <x-admin.button :href="route('admin.warehouses.create')" size="sm">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add warehouse
                </x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'All', 'href' => route('admin.warehouses.index'), 'active' => $status === null],
                    ['label' => 'Active', 'href' => route('admin.warehouses.index', ['is_active' => 1]), 'active' => $status === '1'],
                    ['label' => 'Inactive', 'href' => route('admin.warehouses.index', ['is_active' => 0]), 'active' => $status === '0'],
                ]" />

                <form method="GET" action="{{ route('admin.warehouses.index') }}" class="flex w-full gap-2 lg:w-auto">
                    @if ($status !== null && $status !== '')
                        <input type="hidden" name="is_active" value="{{ $status }}">
                    @endif
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search name, code, city…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <x-slot:mobile>
            <div class="space-y-3">
                @forelse ($warehouses as $warehouse)
                    <article class="rounded-[var(--radius-admin)] border admin-border bg-admin-bg/40 p-3">
                        <div class="min-w-0">
                            <a href="{{ route('admin.warehouses.show', $warehouse) }}" class="block truncate font-medium admin-text hover:text-admin-brand">{{ $warehouse->name }}</a>
                            <p class="truncate text-xs admin-muted">{{ $warehouse->code }} · {{ $warehouse->displayLocation() }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <x-admin.badge :variant="$warehouse->is_active ? 'success' : 'muted'" dot>{{ $warehouse->is_active ? 'Active' : 'Inactive' }}</x-admin.badge>
                                @if ($warehouse->is_default)
                                    <x-admin.badge variant="brand">Default</x-admin.badge>
                                @endif
                                <span class="text-xs admin-muted">{{ (int) ($warehouse->total_stock ?? 0) }} units</span>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 border-t admin-border pt-3">
                            <x-admin.button variant="soft" size="xs" :href="route('admin.warehouses.show', $warehouse)">View</x-admin.button>
                            @if (auth('admin')->user()?->hasPermission('warehouses.manage'))
                                <x-admin.button variant="secondary" size="xs" :href="route('admin.warehouses.edit', $warehouse)">Edit</x-admin.button>
                            @endif
                        </div>
                    </article>
                @empty
                    <x-admin.empty-state
                        title="No warehouses yet"
                        description="Create a warehouse to track stock by fulfillment location."
                        :action-label="auth('admin')->user()?->hasPermission('warehouses.manage') ? 'Add warehouse' : null"
                        :action-href="auth('admin')->user()?->hasPermission('warehouses.manage') ? route('admin.warehouses.create') : null"
                    />
                @endforelse
            </div>
        </x-slot:mobile>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Warehouse</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Location</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Stock units</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($warehouses as $warehouse)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.warehouses.show', $warehouse) }}" class="block font-medium admin-text hover:text-admin-brand">{{ $warehouse->name }}</a>
                        <p class="text-xs admin-muted">{{ $warehouse->code }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $warehouse->displayLocation() }}</td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">{{ (int) ($warehouse->total_stock ?? 0) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1.5">
                            <x-admin.badge :variant="$warehouse->is_active ? 'success' : 'muted'" dot>{{ $warehouse->is_active ? 'Active' : 'Inactive' }}</x-admin.badge>
                            @if ($warehouse->is_default)
                                <x-admin.badge variant="brand">Default</x-admin.badge>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.warehouses.show', $warehouse)" title="View" aria-label="View {{ $warehouse->name }}">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </x-admin.button>
                            @if (auth('admin')->user()?->hasPermission('warehouses.manage'))
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.warehouses.edit', $warehouse)" title="Edit" aria-label="Edit {{ $warehouse->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </x-admin.button>
                                <form method="POST" action="{{ route('admin.warehouses.destroy', $warehouse) }}" onsubmit="return confirm('Delete this warehouse permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="icon-sm" title="Delete" aria-label="Delete {{ $warehouse->name }}">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                    </x-admin.button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-14">
                        <x-admin.empty-state
                            title="No warehouses yet"
                            description="Create a warehouse to track stock by fulfillment location."
                            :action-label="auth('admin')->user()?->hasPermission('warehouses.manage') ? 'Add warehouse' : null"
                            :action-href="auth('admin')->user()?->hasPermission('warehouses.manage') ? route('admin.warehouses.create') : null"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            <x-admin.pagination :paginator="$warehouses" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
