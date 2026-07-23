<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))<script>document.addEventListener('DOMContentLoaded', () => window.adminToast?.push({ title: @json(session('success')), type: 'success' }));</script>@endif
    <x-admin.page-header title="Homepage Reviews" description="Manage approved testimonials shown on the homepage.">
        <x-slot:actions><x-admin.button :href="route('admin.homepage.reviews.create')" size="sm">Add review</x-admin.button></x-slot:actions>
    </x-admin.page-header>
    <x-admin.data-table>
        <thead><tr class="border-b admin-border bg-admin-bg/40"><th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Review</th><th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Rating</th><th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Status</th><th class="px-6 py-3.5 text-right text-xs font-semibold uppercase admin-muted">Actions</th></tr></thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($reviews as $review)
                <tr class="hover:bg-admin-bg/60">
                    <td class="px-6 py-4"><p class="font-medium admin-text">{{ $review->author_name }}</p><p class="text-xs admin-muted">{{ Str::limit($review->body, 80) }}</p></td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $review->rating }}/5</td>
                    <td class="px-6 py-4"><x-admin.badge :variant="$review->is_approved ? 'success' : 'muted'" dot>{{ $review->is_approved ? 'Approved' : 'Pending' }}</x-admin.badge></td>
                    <td class="px-6 py-4"><div class="flex justify-end gap-1"><x-admin.button variant="ghost" size="xs" :href="route('admin.homepage.reviews.edit', $review)">Edit</x-admin.button></div></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-14"><x-admin.empty-state title="No reviews yet" action-label="Add review" :action-href="route('admin.homepage.reviews.create')" /></td></tr>
            @endforelse
        </tbody>
        <x-slot:footer><x-admin.pagination :paginator="$reviews" /></x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
