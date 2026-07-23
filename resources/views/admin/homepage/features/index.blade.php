<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))<script>document.addEventListener('DOMContentLoaded', () => window.adminToast?.push({ title: @json(session('success')), type: 'success' }));</script>@endif
    <x-admin.page-header title="Why Shop Features" description="Why Shop With Us section items.">
        <x-slot:actions><x-admin.button :href="route('admin.homepage.features.create')" size="sm">Add feature</x-admin.button></x-slot:actions>
    </x-admin.page-header>
    <x-admin.data-table>
        <thead><tr class="border-b admin-border bg-admin-bg/40"><th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Feature</th><th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Status</th><th class="px-6 py-3.5 text-right text-xs font-semibold uppercase admin-muted">Actions</th></tr></thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($features as $feature)
                <tr class="hover:bg-admin-bg/60">
                    <td class="px-6 py-4"><p class="font-medium admin-text">{{ $feature->title }}</p><p class="text-xs admin-muted">{{ $feature->description }}</p></td>
                    <td class="px-6 py-4"><x-admin.badge :variant="$feature->is_active ? 'success' : 'muted'" dot>{{ $feature->is_active ? 'Active' : 'Inactive' }}</x-admin.badge></td>
                    <td class="px-6 py-4"><div class="flex justify-end gap-1"><x-admin.button variant="ghost" size="xs" :href="route('admin.homepage.features.edit', $feature)">Edit</x-admin.button></div></td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-5 py-14"><x-admin.empty-state title="No features yet" action-label="Add feature" :action-href="route('admin.homepage.features.create')" /></td></tr>
            @endforelse
        </tbody>
        <x-slot:footer><x-admin.pagination :paginator="$features" /></x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
