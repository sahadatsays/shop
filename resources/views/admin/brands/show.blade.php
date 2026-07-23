<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header :title="$brand->name" description="Brand details and storefront metadata.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.brands.index')">Back</x-admin.button>
            <x-admin.button size="sm" :href="route('admin.brands.edit', $brand)">Edit brand</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Overview">
                <dl>
                    <x-admin.detail-row label="Name" :value="$brand->name" />
                    <x-admin.detail-row label="Slug">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $brand->slug }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$brand->status === \App\Enums\BrandStatus::Active ? 'success' : 'muted'">
                            {{ $brand->status->label() }}
                        </x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Featured">
                        @if ($brand->is_featured)
                            <x-admin.badge variant="brand">Featured</x-admin.badge>
                        @else
                            <span class="admin-muted">No</span>
                        @endif
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Sort order" :value="$brand->sort_order" />
                    <x-admin.detail-row label="Products" :value="$brand->products_count" />
                    <x-admin.detail-row label="Description" :value="$brand->description" />
                    <x-admin.detail-row label="Created" :value="$brand->created_at?->format('M j, Y g:i A')" />
                    <x-admin.detail-row label="Updated" :value="$brand->updated_at?->format('M j, Y g:i A')" />
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="SEO">
                <dl>
                    <x-admin.detail-row label="Meta title" :value="$brand->meta_title" />
                    <x-admin.detail-row label="Meta description" :value="$brand->meta_description" />
                    <x-admin.detail-row label="Meta keywords" :value="$brand->meta_keywords" />
                </dl>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Logo">
                @if ($brand->logoUrl())
                    <img src="{{ $brand->logoUrl() }}" alt="" class="aspect-square w-full max-w-xs rounded-[var(--radius-admin-lg)] object-contain bg-admin-bg p-4 ring-1 ring-black/5">
                @else
                    <div class="flex aspect-square max-w-xs items-center justify-center rounded-[var(--radius-admin-lg)] border border-dashed admin-border bg-admin-bg text-sm admin-muted">No logo</div>
                @endif
            </x-admin.form-card>

            <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Move this brand to trash?')">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" variant="danger-ghost" class="w-full">Move to trash</x-admin.button>
            </form>
        </div>
    </div>
</x-layouts.admin>
