<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))<script>document.addEventListener('DOMContentLoaded', () => window.adminToast?.push({ title: @json(session('success')), type: 'success' }));</script>@endif
    <x-admin.page-header title="Promo Banners" description="Homepage promotional banner grid.">
        <x-slot:actions><x-admin.button :href="route('admin.homepage.promo-banners.create')" size="sm">Add banner</x-admin.button></x-slot:actions>
    </x-admin.page-header>
    <x-admin.data-table>
        <thead><tr class="border-b admin-border bg-admin-bg/40"><th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Banner</th><th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Layout</th><th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Status</th><th class="px-6 py-3.5 text-right text-xs font-semibold uppercase admin-muted">Actions</th></tr></thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($banners as $banner)
                <tr class="hover:bg-admin-bg/60">
                    <td class="px-6 py-4 font-medium admin-text">{{ $banner->title }}</td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $banner->layout->label() }}</td>
                    <td class="px-6 py-4"><x-admin.badge :variant="$banner->is_active ? 'success' : 'muted'" dot>{{ $banner->is_active ? 'Active' : 'Inactive' }}</x-admin.badge></td>
                    <td class="px-6 py-4"><div class="flex justify-end gap-1"><x-admin.button variant="ghost" size="xs" :href="route('admin.homepage.promo-banners.edit', $banner)">Edit</x-admin.button></div></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-14"><x-admin.empty-state title="No promo banners yet" action-label="Add banner" :action-href="route('admin.homepage.promo-banners.create')" /></td></tr>
            @endforelse
        </tbody>
        <x-slot:footer><x-admin.pagination :paginator="$banners" /></x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
