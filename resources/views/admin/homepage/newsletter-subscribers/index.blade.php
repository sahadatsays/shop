<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Newsletter Subscribers" description="View storefront newsletter signups." />

    <x-admin.data-table>
        <x-slot:toolbar>
            <form method="GET" class="flex w-full gap-2 lg:ml-auto lg:w-auto">
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search email…" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 px-3 text-sm admin-text admin-focus-ring lg:w-72">
                <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
            </form>
        </x-slot:toolbar>
        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Email</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Status</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Subscribed</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($subscribers as $subscriber)
                <tr class="hover:bg-admin-bg/60">
                    <td class="px-6 py-4 font-medium admin-text">{{ $subscriber->email }}</td>
                    <td class="px-6 py-4"><x-admin.badge variant="success" dot>{{ $subscriber->status->value }}</x-admin.badge></td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $subscriber->subscribed_at?->format('M j, Y g:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-5 py-14"><x-admin.empty-state title="No subscribers yet" /></td></tr>
            @endforelse
        </tbody>
        <x-slot:footer><x-admin.pagination :paginator="$subscribers" /></x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
