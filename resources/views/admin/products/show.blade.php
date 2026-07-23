@php
    use App\Support\MoneyFormatter;
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header :title="$product->name" description="Product details, inventory, and catalog metadata.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.products.index')">Back</x-admin.button>
            <x-admin.button size="sm" :href="route('admin.products.edit', $product)">Edit product</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Overview">
                <dl>
                    <x-admin.detail-row label="Name" :value="$product->name" />
                    <x-admin.detail-row label="Slug">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $product->slug }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="SKU">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $product->sku }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Barcode" :value="$product->barcode" />
                    <x-admin.detail-row label="Category" :value="$product->category?->name" />
                    <x-admin.detail-row label="Brand" :value="$product->brand?->name" />
                    <x-admin.detail-row label="Price" :value="MoneyFormatter::format($product->price_cents)" />
                    <x-admin.detail-row label="Stock" :value="$product->stock_quantity" />
                    <x-admin.detail-row label="Low stock threshold" :value="$product->low_stock_threshold" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$product->status->badgeVariant()" dot>{{ $product->status->label() }}</x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Featured">
                        @if ($product->is_featured)
                            <x-admin.badge variant="brand">Featured</x-admin.badge>
                        @else
                            <span class="admin-muted">No</span>
                        @endif
                    </x-admin.detail-row>
                    <x-admin.detail-row label="New arrival">
                        @if ($product->is_new_arrival)
                            <x-admin.badge variant="info">New arrival</x-admin.badge>
                        @else
                            <span class="admin-muted">No</span>
                        @endif
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Sort order" :value="$product->sort_order" />
                    <x-admin.detail-row label="Short description" :value="$product->short_description" />
                    <x-admin.detail-row label="Description" :value="$product->description" />
                    <x-admin.detail-row label="Created" :value="$product->created_at?->format('M j, Y g:i A')" />
                    <x-admin.detail-row label="Updated" :value="$product->updated_at?->format('M j, Y g:i A')" />
                </dl>
            </x-admin.form-card>

            @if ($product->specifications->isNotEmpty())
                <x-admin.form-card title="Specifications">
                    <dl>
                        @foreach ($product->specifications as $specification)
                            <x-admin.detail-row :label="$specification->name" :value="$specification->value" />
                        @endforeach
                    </dl>
                </x-admin.form-card>
            @endif

            @if ($product->attributes->isNotEmpty())
                <x-admin.form-card title="Attributes">
                    <dl>
                        @foreach ($product->attributes as $attribute)
                            <x-admin.detail-row :label="$attribute->name" :value="$attribute->value" />
                        @endforeach
                    </dl>
                </x-admin.form-card>
            @endif

            @if ($product->relatedProducts->isNotEmpty())
                <x-admin.form-card title="Related products">
                    <ul class="divide-y divide-admin-border/60">
                        @foreach ($product->relatedProducts as $related)
                            <li class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                                <div>
                                    <a href="{{ route('admin.products.show', $related) }}" class="font-medium admin-text hover:text-admin-brand">{{ $related->name }}</a>
                                    <p class="text-xs admin-muted">{{ $related->sku }}</p>
                                </div>
                                <span class="text-sm admin-text-secondary">{{ MoneyFormatter::format($related->price_cents) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </x-admin.form-card>
            @endif

            <x-admin.form-card title="SEO">
                <dl>
                    <x-admin.detail-row label="Meta title" :value="$product->meta_title" />
                    <x-admin.detail-row label="Meta description" :value="$product->meta_description" />
                    <x-admin.detail-row label="Meta keywords" :value="$product->meta_keywords" />
                </dl>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Gallery">
                @if ($product->images->isNotEmpty())
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($product->images as $image)
                            <div class="relative">
                                <img src="{{ $image->url() }}" alt="{{ $image->alt_text }}" class="aspect-square w-full rounded-[var(--radius-admin)] object-cover ring-1 ring-admin-border">
                                @if ($image->is_primary)
                                    <span class="absolute left-2 top-2 rounded-full bg-admin-brand px-2 py-0.5 text-[10px] font-medium text-white">Primary</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex aspect-video items-center justify-center rounded-[var(--radius-admin-lg)] border border-dashed admin-border bg-admin-bg text-sm admin-muted">No images</div>
                @endif
            </x-admin.form-card>

            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Move this product to trash?')">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" variant="danger-ghost" class="w-full">Move to trash</x-admin.button>
            </form>
        </div>
    </div>
</x-layouts.admin>
