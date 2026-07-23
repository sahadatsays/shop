<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Countdown Promotions" description="Time-limited sale callouts with required end dates.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('promotions.manage'))
                <x-admin.button :href="route('admin.countdown-promotions.create')" size="sm">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add countdown
                </x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <form method="GET" action="{{ route('admin.countdown-promotions.index') }}" class="flex w-full gap-2 lg:ml-auto lg:w-auto">
                <div class="relative min-w-0 flex-1 lg:w-72">
                    <input
                        type="search"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search countdowns…"
                        class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 px-3 text-sm admin-text admin-focus-ring"
                    >
                </div>
                <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
            </form>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Countdown</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Placement</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Ends</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($promotions as $promotion)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <p class="font-medium admin-text">{{ $promotion->name }}</p>
                        <p class="truncate text-xs admin-muted">{{ $promotion->headline }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $promotion->placement->label() }}</td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">
                        {{ $promotion->ends_at?->format('M j, Y g:i A') ?? '—' }}
                    </td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$promotion->is_active ? 'success' : 'muted'" dot>
                            {{ $promotion->is_active ? 'Active' : 'Inactive' }}
                        </x-admin.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            @if (auth('admin')->user()?->hasPermission('promotions.manage'))
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.countdown-promotions.edit', $promotion)" title="Edit" aria-label="Edit {{ $promotion->name }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </x-admin.button>
                                <form method="POST" action="{{ route('admin.countdown-promotions.destroy', $promotion) }}" onsubmit="return confirm('Delete this countdown?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="icon-sm" title="Delete" aria-label="Delete {{ $promotion->name }}">
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
                            title="No countdown promotions"
                            description="Create urgency-driven promotions with a fixed end date."
                            action-label="Add countdown"
                            :action-href="route('admin.countdown-promotions.create')"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            <x-admin.pagination :paginator="$promotions" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
