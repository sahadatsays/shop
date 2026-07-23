<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => window.adminToast?.push({ title: @json(session('success')), type: 'success' }));</script>
    @endif

    <x-admin.page-header title="Hero Banners" description="Manage homepage hero slider slides.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('homepage.manage'))
                <x-admin.button :href="route('admin.homepage.hero-banners.create')" size="sm">Add banner</x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <form method="GET" class="flex w-full gap-2 lg:ml-auto lg:w-auto">
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search banners…" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 px-3 text-sm admin-text admin-focus-ring lg:w-72">
                <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
            </form>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Banner</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Schedule</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($banners as $banner)
                <tr class="hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <p class="font-medium admin-text">{{ $banner->title }}</p>
                        <p class="text-xs admin-muted">{{ $banner->subtitle }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">
                        {{ $banner->starts_at?->format('M j') ?? 'Any' }} → {{ $banner->ends_at?->format('M j, Y') ?? 'No end' }}
                    </td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$banner->is_active ? 'success' : 'muted'" dot>{{ $banner->is_active ? 'Active' : 'Inactive' }}</x-admin.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-1">
                            @if (auth('admin')->user()?->hasPermission('homepage.manage'))
                                <x-admin.button variant="ghost" size="icon-sm" :href="route('admin.homepage.hero-banners.edit', $banner)" title="Edit">✎</x-admin.button>
                                <form method="POST" action="{{ route('admin.homepage.hero-banners.destroy', $banner) }}" onsubmit="return confirm('Delete this banner?')">
                                    @csrf @method('DELETE')
                                    <x-admin.button type="submit" variant="danger-ghost" size="icon-sm" title="Delete">×</x-admin.button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-14"><x-admin.empty-state title="No hero banners yet" description="Create slides for the homepage hero slider." action-label="Add banner" :action-href="route('admin.homepage.hero-banners.create')" /></td></tr>
            @endforelse
        </tbody>
        <x-slot:footer><x-admin.pagination :paginator="$banners" /></x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
