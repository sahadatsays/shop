@php
    $brand = $form->brand;
@endphp

<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    x-data="categoryForm(@js([
        'name' => old('name', $brand?->name ?? ''),
        'slug' => old('slug', $brand?->slug ?? ''),
        'autoSlug' => ! $brand,
        'imageUrl' => $brand?->logoUrl(),
    ]))"
    class="space-y-8"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <x-admin.section title="Basic information" description="Brand identity, status, and featured placement.">
        <div class="grid gap-5 lg:grid-cols-2">
            <x-admin.input label="Name" name="name" x-model="name" @input="onNameInput" :value="old('name', $brand?->name)" required />
            <div>
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium admin-text">Slug</span>
                    <input type="text" name="slug" x-model="slug" @input="onSlugInput"
                           value="{{ old('slug', $brand?->slug) }}"
                           class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3 py-2 text-sm admin-text admin-focus-ring"
                           placeholder="auto-generated-from-name">
                </label>
                <p class="mt-1 text-xs admin-muted">URL-friendly identifier. Auto-generated from name unless edited.</p>
            </div>
            <x-admin.select label="Status" name="status">
                @foreach (\App\Enums\BrandStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $brand?->status?->value ?? 'active') === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </x-admin.select>
            <x-admin.input label="Sort order" name="sort_order" type="number" min="0" :value="old('sort_order', $brand?->sort_order ?? 0)" />
            <div class="lg:col-span-2">
                <x-admin.checkbox
                    label="Featured brand"
                    name="is_featured"
                    :checked="(bool) old('is_featured', $brand?->is_featured)"
                    value="1"
                />
                <p class="mt-1 text-xs admin-muted">Featured brands appear on the admin dashboard and storefront highlights.</p>
            </div>
            <div class="lg:col-span-2">
                <x-admin.textarea label="Description" name="description" rows="4" placeholder="Brand story and positioning">{{ old('description', $brand?->description) }}</x-admin.textarea>
            </div>
        </div>
    </x-admin.section>

    <x-admin.section title="Logo" description="Upload a brand logo for listings and featured widgets.">
        <div class="max-w-md">
            <x-admin.file-input label="Brand logo" name="logo" preview="imagePreview" :current="$brand?->logoUrl()" />
        </div>
    </x-admin.section>

    <x-admin.section title="SEO" description="Search engine metadata for this brand page.">
        <div class="grid gap-5">
            <x-admin.input label="Meta title" name="meta_title" :value="old('meta_title', $brand?->meta_title)" placeholder="Defaults to brand name if empty" />
            <x-admin.textarea label="Meta description" name="meta_description" rows="3" placeholder="Brief description for search results">{{ old('meta_description', $brand?->meta_description) }}</x-admin.textarea>
            <x-admin.input label="Meta keywords" name="meta_keywords" :value="old('meta_keywords', $brand?->meta_keywords)" placeholder="comma, separated, keywords" />
        </div>
    </x-admin.section>

    <div class="flex flex-wrap items-center gap-3 border-t admin-border pt-6">
        <x-admin.button type="submit">{{ $submitLabel }}</x-admin.button>
        <x-admin.button variant="secondary" :href="route('admin.brands.index')">Cancel</x-admin.button>
    </div>
</form>
