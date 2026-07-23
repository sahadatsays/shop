@php
    $collection ??= null;
    $formatDatetimeLocal = fn ($value) => $value ? $value->format('Y-m-d\TH:i') : '';
    $selectedProductIds = old('product_ids', $collection ? $collection->products->pluck('id')->all() : []);
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
    x-data="categoryForm(@js([
        'name' => old('name', $collection?->name ?? ''),
        'slug' => old('slug', $collection?->slug ?? ''),
        'autoSlug' => ! $collection && ! old('slug'),
        'imageUrl' => $collection?->imageUrl(),
        'bannerUrl' => $collection?->bannerUrl(),
    ]))"
    class="space-y-6"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Collection details">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Name"
                        name="name"
                        x-model="name"
                        @input="onNameInput"
                        :value="old('name', $collection?->name ?? '')"
                        required
                        class="sm:col-span-2"
                    />

                    <x-admin.field label="Slug" name="slug" help="Leave blank to auto-generate from name." class="sm:col-span-2">
                        <input
                            type="text"
                            name="slug"
                            id="field-slug"
                            x-model="slug"
                            @input="onSlugInput"
                            value="{{ old('slug', $collection?->slug ?? '') }}"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3.5 py-2.5 font-mono text-sm admin-text admin-focus-ring"
                        >
                    </x-admin.field>

                    <x-admin.textarea
                        label="Description"
                        name="description"
                        rows="4"
                        class="sm:col-span-2"
                    >{{ old('description', $collection?->description ?? '') }}</x-admin.textarea>

                    <x-admin.input
                        label="Sort order"
                        name="sort_order"
                        type="number"
                        min="0"
                        :value="old('sort_order', $collection?->sort_order ?? 0)"
                    />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Products" description="Select products to include in this collection.">
                <x-admin.field label="Products" name="product_ids" help="Hold Cmd/Ctrl to select multiple products.">
                    <select
                        name="product_ids[]"
                        id="field-product_ids"
                        multiple
                        size="8"
                        class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3.5 py-2.5 text-sm admin-text admin-focus-ring"
                    >
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(in_array($product->id, $selectedProductIds))>
                                {{ $product->name }} ({{ $product->sku }})
                            </option>
                        @endforeach
                    </select>
                </x-admin.field>
            </x-admin.form-card>

            <x-admin.form-card title="Schedule">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Starts at"
                        name="starts_at"
                        type="datetime-local"
                        :value="old('starts_at', $formatDatetimeLocal($collection?->starts_at))"
                    />
                    <x-admin.input
                        label="Ends at"
                        name="ends_at"
                        type="datetime-local"
                        :value="old('ends_at', $formatDatetimeLocal($collection?->ends_at))"
                    />
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Visibility">
                <div class="space-y-4">
                    <x-admin.checkbox
                        label="Featured collection"
                        name="is_featured"
                        :checked="(bool) old('is_featured', $collection?->is_featured ?? false)"
                        help="Featured collections appear in homepage highlights."
                    />
                    <x-admin.checkbox
                        label="Active"
                        name="is_active"
                        :checked="(bool) old('is_active', $collection?->is_active ?? true)"
                    />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Cover image">
                <x-admin.image-upload
                    label="Collection image"
                    name="image"
                    preview="imagePreview"
                    :current="$collection?->imageUrl()"
                    aspect="square"
                    help="Square cover for cards. Max 4MB."
                />
            </x-admin.form-card>

            <x-admin.form-card title="Banner">
                <x-admin.image-upload
                    label="Collection banner"
                    name="banner"
                    preview="bannerPreview"
                    :current="$collection?->bannerUrl()"
                    aspect="banner"
                    help="Wide banner for hero sections. Max 4MB."
                />
            </x-admin.form-card>

            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" class="flex-1">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="$cancelRoute" class="flex-1">Cancel</x-admin.button>
            </div>
        </div>
    </div>
</form>
