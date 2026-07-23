<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header :title="$category->name" description="Category details and storefront metadata.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.categories.index')">Back</x-admin.button>
            <x-admin.button size="sm" :href="route('admin.categories.edit', $category)">Edit category</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Overview">
                <dl>
                    <x-admin.detail-row label="Name" :value="$category->name" />
                    <x-admin.detail-row label="Slug">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $category->slug }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Parent" :value="$category->parent?->name" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$category->status === \App\Enums\CategoryStatus::Active ? 'success' : 'muted'">
                            {{ $category->status->label() }}
                        </x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Sort order" :value="$category->sort_order" />
                    <x-admin.detail-row label="Products" :value="$category->products_count" />
                    <x-admin.detail-row label="Created" :value="$category->created_at?->format('M j, Y g:i A')" />
                    <x-admin.detail-row label="Updated" :value="$category->updated_at?->format('M j, Y g:i A')" />
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="SEO">
                <dl>
                    <x-admin.detail-row label="Meta title" :value="$category->meta_title" />
                    <x-admin.detail-row label="Meta description" :value="$category->meta_description" />
                    <x-admin.detail-row label="Meta keywords" :value="$category->meta_keywords" />
                </dl>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Media">
                <div class="space-y-4">
                    <div>
                        <p class="mb-2 text-xs font-medium uppercase tracking-wide admin-muted">Image</p>
                        @if ($category->imageUrl())
                            <img src="{{ $category->imageUrl() }}" alt="" class="aspect-square w-full max-w-xs rounded-[var(--radius-admin-lg)] object-cover ring-1 ring-black/5">
                        @else
                            <div class="flex aspect-square max-w-xs items-center justify-center rounded-[var(--radius-admin-lg)] border border-dashed admin-border bg-admin-bg text-sm admin-muted">No image</div>
                        @endif
                    </div>
                    <div>
                        <p class="mb-2 text-xs font-medium uppercase tracking-wide admin-muted">Banner</p>
                        @if ($category->bannerUrl())
                            <img src="{{ $category->bannerUrl() }}" alt="" class="aspect-[16/7] w-full rounded-[var(--radius-admin-lg)] object-cover ring-1 ring-black/5">
                        @else
                            <div class="flex aspect-[16/7] items-center justify-center rounded-[var(--radius-admin-lg)] border border-dashed admin-border bg-admin-bg text-sm admin-muted">No banner</div>
                        @endif
                    </div>
                </div>
            </x-admin.form-card>

            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Move this category to trash?')">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" variant="danger-ghost" class="w-full">Move to trash</x-admin.button>
            </form>
        </div>
    </div>
</x-layouts.admin>
