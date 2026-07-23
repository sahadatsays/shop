@php
    use App\Enums\CustomerStatus;

    $isTrashed = (bool) ($filters['trashed'] ?? false);
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

    <x-admin.page-header title="Customers" description="Manage customer profiles, addresses, notes, and purchase history.">
        <x-slot:actions>
            @unless ($isTrashed)
                <x-admin.button :href="route('admin.customers.create')" size="sm">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add customer
                </x-admin.button>
            @endunless
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'All', 'href' => route('admin.customers.index'), 'active' => ! $isTrashed && ! $status],
                    ['label' => 'Active', 'href' => route('admin.customers.index', ['status' => CustomerStatus::Active->value]), 'active' => $status === CustomerStatus::Active->value],
                    ['label' => 'Inactive', 'href' => route('admin.customers.index', ['status' => CustomerStatus::Inactive->value]), 'active' => $status === CustomerStatus::Inactive->value],
                    ['label' => 'Suspended', 'href' => route('admin.customers.index', ['status' => CustomerStatus::Suspended->value]), 'active' => $status === CustomerStatus::Suspended->value],
                    ['label' => 'Trashed', 'href' => route('admin.customers.index', ['trashed' => 1]), 'active' => $isTrashed],
                ]" />

                <form method="GET" action="{{ route('admin.customers.index') }}" class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
                    @if ($isTrashed)
                        <input type="hidden" name="trashed" value="1">
                    @endif
                    @if ($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <select name="has_orders" class="rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 py-2.5 text-sm admin-text admin-focus-ring">
                        <option value="">All order activity</option>
                        <option value="1" @selected(($filters['has_orders'] ?? null) === '1')>Has orders</option>
                        <option value="0" @selected(($filters['has_orders'] ?? null) === '0')>No orders</option>
                    </select>
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search name, email, phone…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <x-slot:mobile>
            <div class="space-y-3">
                @forelse ($customers as $customer)
                    <article class="rounded-[var(--radius-admin)] border admin-border bg-admin-bg/40 p-3">
                        <div class="flex items-start gap-3">
                            <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-admin-accent-muted text-xs font-semibold admin-muted">
                                {{ $customer->initials() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="block truncate font-medium admin-text hover:text-admin-brand">{{ $customer->name }}</a>
                                <p class="truncate text-xs admin-muted">{{ $customer->email }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <x-admin.badge :variant="$customer->status->badgeVariant()" dot>{{ $customer->status->label() }}</x-admin.badge>
                                    <span class="text-xs admin-muted">{{ $customer->orders_count }} orders</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 border-t admin-border pt-3">
                            @if ($isTrashed)
                                <form method="POST" action="{{ route('admin.customers.restore', $customer) }}">
                                    @csrf
                                    <x-admin.button type="submit" variant="soft" size="xs">Restore</x-admin.button>
                                </form>
                            @else
                                <x-admin.button variant="soft" size="xs" :href="route('admin.customers.show', $customer)">View</x-admin.button>
                                <x-admin.button variant="secondary" size="xs" :href="route('admin.customers.edit', $customer)">Edit</x-admin.button>
                            @endif
                        </div>
                    </article>
                @empty
                    <x-admin.empty-state
                        title="{{ $isTrashed ? 'Trash is empty' : 'No customers yet' }}"
                        description="{{ $isTrashed ? 'Deleted customers will appear here.' : 'Create a customer profile to start tracking orders and notes.' }}"
                        :action-label="$isTrashed ? null : 'Add customer'"
                        :action-href="$isTrashed ? null : route('admin.customers.create')"
                    />
                @endforelse
            </div>
        </x-slot:mobile>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Customer</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Phone</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Orders</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Joined</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($customers as $customer)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="flex size-10 items-center justify-center rounded-full bg-admin-accent-muted text-xs font-semibold admin-muted">
                                {{ $customer->initials() }}
                            </span>
                            <div class="min-w-0">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="block truncate font-medium admin-text hover:text-admin-brand">{{ $customer->name }}</a>
                                <p class="truncate text-xs admin-muted">{{ $customer->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $customer->phone ?: '—' }}</td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">{{ $customer->orders_count }}</td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$customer->status->badgeVariant()" dot>{{ $customer->status->label() }}</x-admin.badge>
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $customer->created_at?->format('M j, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            @if ($isTrashed)
                                <form method="POST" action="{{ route('admin.customers.restore', $customer) }}">
                                    @csrf
                                    <x-admin.button type="submit" variant="soft" size="xs">Restore</x-admin.button>
                                </form>
                            @else
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.customers.show', $customer)" title="View" aria-label="View {{ $customer->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </x-admin.button>
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.customers.edit', $customer)" title="Edit" aria-label="Edit {{ $customer->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </x-admin.button>
                                <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('Move this customer to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="icon-sm" title="Delete" aria-label="Delete {{ $customer->name }}">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                    </x-admin.button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-14">
                        <x-admin.empty-state
                            title="{{ $isTrashed ? 'Trash is empty' : 'No customers yet' }}"
                            description="{{ $isTrashed ? 'Deleted customers will appear here.' : 'Create a customer profile to start tracking orders and notes.' }}"
                            :action-label="$isTrashed ? null : 'Add customer'"
                            :action-href="$isTrashed ? null : route('admin.customers.create')"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            <x-admin.pagination :paginator="$customers" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
