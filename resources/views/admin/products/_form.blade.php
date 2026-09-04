@php
    $product = $form->product;
    $price = old('price', $product ? number_format($product->price_cents / 100, 2, '.', '') : '');
    $compareAtPrice = old('compare_at_price', $product && $product->compare_at_price_cents ? number_format($product->compare_at_price_cents / 100, 2, '.', '') : '');
    $specifications = old('specifications', $product?->specifications->map(fn ($s) => ['name' => $s->name, 'value' => $s->value])->values()->all() ?? [['name' => '', 'value' => '']]);
    $attributes = old('attributes', $product?->attributes->map(fn ($a) => ['name' => $a->name, 'value' => $a->value])->values()->all() ?? [['name' => '', 'value' => '']]);
    $relatedIds = old('related_product_ids', $product?->relatedProducts->pluck('id')->all() ?? []);
    $existingImages = $product?->images->map(fn ($image) => ['id' => $image->id, 'url' => $image->url(), 'is_primary' => $image->is_primary])->values()->all() ?? [];
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3 dark:bg-red-950/20" role="alert">
        <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
    </div>
@endif

<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    x-data="productForm(@js([
        'name' => old('name', $product?->name ?? ''),
        'slug' => old('slug', $product?->slug ?? ''),
        'autoSlug' => ! $product && ! old('slug'),
        'specifications' => $specifications,
        'attributes' => $attributes,
        'existingImages' => $existingImages,
    ]))"
    class="space-y-6"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <template x-for="imageId in removeImages" :key="imageId">
        <input type="hidden" name="remove_images[]" :value="imageId">
    </template>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Basic information" description="Product identity, pricing, and inventory.">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Name"
                        name="name"
                        x-model="name"
                        @input="onNameInput"
                        :value="old('name', $product?->name)"
                        placeholder="e.g. Heritage Field Jacket"
                        help="Display name shown on product pages."
                        required
                        class="sm:col-span-2"
                    />

                    <x-admin.field label="Slug" name="slug" help="URL-friendly ID. Auto-generated from name unless you edit it." required>
                        <input
                            type="text"
                            name="slug"
                            id="field-slug"
                            x-model="slug"
                            @input="onSlugInput"
                            value="{{ old('slug', $product?->slug) }}"
                            placeholder="heritage-field-jacket"
                            @class([
                                'block w-full rounded-[var(--radius-admin)] border bg-admin-bg px-3.5 py-2.5 font-mono text-sm admin-text placeholder:admin-muted admin-focus-ring',
                                'border-admin-danger' => $errors->has('slug'),
                                'admin-border' => ! $errors->has('slug'),
                            ])
                        >
                    </x-admin.field>

                    <x-admin.input
                        label="SKU"
                        name="sku"
                        :value="old('sku', $product?->sku)"
                        placeholder="JKT-HFJ-001"
                        help="Unique stock keeping unit."
                        required
                    />

                    <x-admin.input
                        label="Barcode"
                        name="barcode"
                        :value="old('barcode', $product?->barcode)"
                        placeholder="Optional UPC/EAN"
                        help="Optional barcode for scanning."
                    />

                    <x-admin.select label="Category" name="category_id" help="Primary category for this product." required>
                        <option value="">Select category</option>
                        @foreach ($form->categoryOptions as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $product?->category_id) === $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.select label="Brand" name="brand_id" help="Optional brand association.">
                        <option value="">No brand</option>
                        @foreach ($form->brandOptions as $brand)
                            <option value="{{ $brand->id }}" @selected((int) old('brand_id', $product?->brand_id) === $brand->id)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.input
                        label="Price"
                        name="price"
                        type="number"
                        step="0.01"
                        min="0"
                        :value="$price"
                        placeholder="49.99"
                        help="Retail price in BDT."
                        required
                    />

                    <x-admin.input
                        label="Compare-at price"
                        name="compare_at_price"
                        type="number"
                        step="0.01"
                        min="0"
                        :value="$compareAtPrice"
                        placeholder="79.99"
                        help="Original price shown struck through when higher than retail price."
                    />

                    <x-admin.input
                        label="Stock quantity"
                        name="stock_quantity"
                        type="number"
                        min="0"
                        :value="old('stock_quantity', $product?->stock_quantity ?? 0)"
                        placeholder="0"
                        help="Units available for sale."
                        required
                    />

                    <x-admin.input
                        label="Low stock threshold"
                        name="low_stock_threshold"
                        type="number"
                        min="0"
                        :value="old('low_stock_threshold', $product?->low_stock_threshold ?? 10)"
                        placeholder="10"
                        help="Alert when stock falls to this level."
                    />

                    <x-admin.select label="Status" name="status" help="Only published products appear on the storefront." required>
                        @foreach (\App\Enums\ProductStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $product?->status?->value ?? 'draft') === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.input
                        label="Sort order"
                        name="sort_order"
                        type="number"
                        min="0"
                        :value="old('sort_order', $product?->sort_order ?? 0)"
                        placeholder="0"
                        help="Lower numbers appear first."
                    />

                    <x-admin.textarea
                        label="Short description"
                        name="short_description"
                        rows="2"
                        placeholder="Brief summary for listings…"
                        help="Shown in product cards and search results."
                        class="sm:col-span-2"
                    >{{ old('short_description', $product?->short_description) }}</x-admin.textarea>

                    <x-admin.textarea
                        label="Description"
                        name="description"
                        rows="5"
                        placeholder="Full product description…"
                        help="Detailed product information for the product page."
                        class="sm:col-span-2"
                    >{{ old('description', $product?->description) }}</x-admin.textarea>
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Specifications" description="Technical details such as dimensions, materials, or weight.">
                <div class="space-y-3">
                    <template x-for="(spec, index) in specifications" :key="index">
                        <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
                            <input
                                type="text"
                                :name="`specifications[${index}][name]`"
                                x-model="spec.name"
                                placeholder="Name (e.g. Weight)"
                                class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                            >
                            <input
                                type="text"
                                :name="`specifications[${index}][value]`"
                                x-model="spec.value"
                                placeholder="Value (e.g. 2.5 lbs)"
                                class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                            >
                            <button type="button" @click="removeSpecification(index)" class="rounded-[var(--radius-admin)] border admin-border px-3 py-2.5 text-sm admin-muted transition-colors hover:bg-admin-bg hover:admin-text admin-focus-ring">
                                Remove
                            </button>
                        </div>
                    </template>
                    <x-admin.button type="button" variant="secondary" size="sm" @click="addSpecification">Add specification</x-admin.button>
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Attributes" description="Merchandising attributes for filters and variants.">
                <div class="space-y-3">
                    <template x-for="(attr, index) in attributes" :key="index">
                        <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
                            <input
                                type="text"
                                :name="`attributes[${index}][name]`"
                                x-model="attr.name"
                                placeholder="Name (e.g. Color)"
                                class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                            >
                            <input
                                type="text"
                                :name="`attributes[${index}][value]`"
                                x-model="attr.value"
                                placeholder="Value (e.g. Olive Drab)"
                                class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                            >
                            <button type="button" @click="removeAttribute(index)" class="rounded-[var(--radius-admin)] border admin-border px-3 py-2.5 text-sm admin-muted transition-colors hover:bg-admin-bg hover:admin-text admin-focus-ring">
                                Remove
                            </button>
                        </div>
                    </template>
                    <x-admin.button type="button" variant="secondary" size="sm" @click="addAttribute">Add attribute</x-admin.button>
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Related products" description="Cross-sell suggestions shown on the product page.">
                <x-admin.field label="Related products" name="related_product_ids" help="Select products to recommend alongside this one.">
                    <select
                        name="related_product_ids[]"
                        id="field-related_product_ids"
                        multiple
                        size="6"
                        class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3.5 py-2.5 text-sm admin-text admin-focus-ring"
                    >
                        @foreach ($form->relatedProductOptions as $option)
                            <option value="{{ $option->id }}" @selected(in_array($option->id, $relatedIds))>
                                {{ $option->name }} ({{ $option->sku }})
                            </option>
                        @endforeach
                    </select>
                </x-admin.field>
            </x-admin.form-card>

            <x-admin.form-card title="SEO" description="Search engine metadata for this product page.">
                <div class="space-y-5">
                    <x-admin.input
                        label="Meta title"
                        name="meta_title"
                        :value="old('meta_title', $product?->meta_title)"
                        placeholder="Leave blank to use the product name"
                        help="Recommended under 60 characters."
                    />
                    <x-admin.textarea
                        label="Meta description"
                        name="meta_description"
                        rows="3"
                        placeholder="A short summary for search results…"
                        help="Recommended under 160 characters."
                    >{{ old('meta_description', $product?->meta_description) }}</x-admin.textarea>
                    <x-admin.input
                        label="Meta keywords"
                        name="meta_keywords"
                        :value="old('meta_keywords', $product?->meta_keywords)"
                        placeholder="jacket, outdoor, veteran"
                        help="Comma-separated keywords."
                    />
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Visibility" description="Featured and new arrival placement.">
                <div class="space-y-4">
                    <x-admin.checkbox
                        label="Featured product"
                        name="is_featured"
                        :checked="(bool) old('is_featured', $product?->is_featured)"
                        help="Featured products appear in homepage highlights."
                    />
                    <x-admin.checkbox
                        label="New arrival"
                        name="is_new_arrival"
                        :checked="(bool) old('is_new_arrival', $product?->is_new_arrival)"
                        help="Mark as a new arrival for storefront badges."
                    />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Gallery" description="Product images for listings and detail pages.">
                <template x-if="existingImages.length">
                    <div class="mb-4 grid grid-cols-2 gap-3">
                        <template x-for="image in existingImages" :key="image.id">
                            <div class="relative">
                                <img
                                    :src="image.url"
                                    alt=""
                                    class="aspect-square w-full rounded-[var(--radius-admin)] object-cover ring-1 ring-admin-border"
                                    :class="{ 'opacity-40': isImageMarkedForRemoval(image.id) }"
                                >
                                <button
                                    type="button"
                                    @click="toggleRemoveImage(image.id)"
                                    class="absolute right-2 top-2 rounded-[var(--radius-admin)] bg-admin-surface/90 px-2 py-1 text-xs font-medium admin-text shadow-sm admin-focus-ring"
                                    x-text="isImageMarkedForRemoval(image.id) ? 'Undo' : 'Remove'"
                                ></button>
                            </div>
                        </template>
                    </div>
                </template>

                <x-admin.field label="Upload images" name="gallery" help="Add one or more images. First image becomes primary if none exists. Max 4MB each.">
                    <input
                        type="file"
                        name="gallery[]"
                        id="field-gallery"
                        accept="image/*"
                        multiple
                        class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3 py-2 text-sm admin-text file:mr-3 file:rounded-[var(--radius-admin)] file:border-0 file:bg-admin-accent-muted file:px-3 file:py-1.5 file:text-sm file:font-medium file:admin-text admin-focus-ring"
                    >
                </x-admin.field>
            </x-admin.form-card>

            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" class="flex-1">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.products.index')" class="flex-1">Cancel</x-admin.button>
            </div>
        </div>
    </div>
</form>
