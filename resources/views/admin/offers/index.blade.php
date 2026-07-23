<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Offers" description="Curated promotional events with linked products and discounts.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('offers.manage'))
                <x-admin.button :href="route('admin.offers.create')" size="sm">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add offer
                </x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <form method="GET" action="{{ route('admin.offers.index') }}" class="flex w-full gap-2 lg:ml-auto lg:w-auto">
                <div class="relative min-w-0 flex-1 lg:w-72">
                    <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                    <input
                        type="search"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search offers…"
                        class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                    >
                </div>
                <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
            </form>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Offer</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Products</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Discount</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($offers as $offer)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.offers.show', $offer) }}" class="block font-medium admin-text hover:text-admin-brand">{{ $offer->name }}</a>
                        <p class="truncate text-xs admin-muted">{{ $offer->headline }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm tabular-nums admin-text-secondary">{{ $offer->products_count }}</td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">
                        {{ $offer->discount?->code ?? '—' }}
                    </td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$offer->is_active ? 'success' : 'muted'" dot>
                            {{ $offer->is_active ? 'Active' : 'Inactive' }}
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.offers.show', $offer)" title="View" aria-label="View {{ $offer->name }}">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </x-admin.button>
                            @if (auth('admin')->user()?->hasPermission('offers.manage'))
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.offers.edit', $offer)" title="Edit" aria-label="Edit {{ $offer->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </x-admin.button>
                                <form method="POST" action="{{ route('admin.offers.destroy', $offer) }}" onsubmit="return confirm('Delete this offer?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="icon-sm" title="Delete" aria-label="Delete {{ $offer->name }}">
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
                            title="No offers yet"
                            description="Create promotional events with featured products."
                            action-label="Add offer"
                            :action-href="route('admin.offers.create')"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            <x-admin.pagination :paginator="$offers" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
