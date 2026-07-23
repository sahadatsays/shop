@php
    $isActive = ($filters['active'] ?? null) === '1';
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Discounts" description="Manage coupon codes and order-level discounts.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('discounts.manage'))
                <x-admin.button :href="route('admin.discounts.create')" size="sm">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add discount
                </x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs :tabs="[
                    ['label' => 'All', 'href' => route('admin.discounts.index'), 'active' => ! $isActive],
                    ['label' => 'Active', 'href' => route('admin.discounts.index', ['active' => 1]), 'active' => $isActive],
                ]" />

                <form method="GET" action="{{ route('admin.discounts.index') }}" class="flex w-full gap-2 lg:w-auto">
                    @if ($isActive)
                        <input type="hidden" name="active" value="1">
                    @endif
                    <div class="relative min-w-0 flex-1 lg:w-72">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search code or name…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                        >
                    </div>
                    <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                </form>
            </div>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Code</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Value</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Uses</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Schedule</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($discounts as $discount)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <p class="font-medium admin-text">{{ $discount->name }}</p>
                        <code class="mt-0.5 inline-block rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs admin-muted">{{ $discount->code }}</code>
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $discount->formattedValue() }}</td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">
                        {{ $discount->used_count }}@if ($discount->max_uses)/{{ $discount->max_uses }}@endif
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">
                        @if ($discount->starts_at || $discount->ends_at)
                            <span>{{ $discount->starts_at?->format('M j, Y') ?? 'Any time' }}</span>
                            <span class="admin-muted">→</span>
                            <span>{{ $discount->ends_at?->format('M j, Y') ?? 'No end' }}</span>
                        @else
                            <span class="admin-muted">Always on</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$discount->isAvailable() ? 'success' : 'muted'" dot>
                            {{ $discount->is_active ? ($discount->isAvailable() ? 'Active' : 'Scheduled / expired') : 'Inactive' }}
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            @if (auth('admin')->user()?->hasPermission('discounts.manage'))
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.discounts.edit', $discount)" title="Edit" aria-label="Edit {{ $discount->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </x-admin.button>
                                <form method="POST" action="{{ route('admin.discounts.destroy', $discount) }}" onsubmit="return confirm('Delete this discount?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="icon-sm" title="Delete" aria-label="Delete {{ $discount->name }}">
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
                            title="No discounts yet"
                            description="Create coupon codes for checkout and promotional offers."
                            action-label="Add discount"
                            :action-href="route('admin.discounts.create')"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            <x-admin.pagination :paginator="$discounts" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
