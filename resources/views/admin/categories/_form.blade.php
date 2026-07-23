@php
    $category = $form->category;
@endphp

<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    x-data="categoryForm(@js([
        'name' => old('name', $category?->name ?? ''),
        'slug' => old('slug', $category?->slug ?? ''),
        'autoSlug' => ! $category,
        'imageUrl' => $category?->imageUrl(),
        'bannerUrl' => $category?->bannerUrl(),
    ]))"
    class="space-y-8"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <x-admin.section title="Basic information" description="Name, slug, hierarchy, and display order.">
        <div class="grid gap-5 lg:grid-cols-2">
            <x-admin.input label="Name" name="name" x-model="name" @input="onNameInput" :value="old('name', $category?->name)" required />
            <div>
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium admin-text">Slug</span>
                    <input type="text" name="slug" x-model="slug" @input="onSlugInput"
                           value="{{ old('slug', $category?->slug) }}"
                           class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3 py-2 text-sm admin-text admin-focus-ring"
                           placeholder="auto-generated-from-name">
                </label>
                <p class="mt-1 text-xs admin-muted">URL-friendly identifier. Auto-generated from name unless edited.</p>
            </div>
            <x-admin.select label="Parent category" name="parent_id">
                <option value="">None (top-level)</option>
                @foreach ($form->parentOptions as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id', $category?->parent_id) == $parent->id)>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </x-admin.select>
            <x-admin.input label="Sort order" name="sort_order" type="number" min="0" :value="old('sort_order', $category?->sort_order ?? 0)" />
            <x-admin.select label="Status" name="status">
                @foreach (\App\Enums\CategoryStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $category?->status?->value ?? 'active') === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </x-admin.select>
        </div>
    </x-admin.section>

    <x-admin.section title="Media" description="Category thumbnail and banner for storefront display.">
        <div class="grid gap-5 lg:grid-cols-2">
            <x-admin.file-input label="Category image" name="image" preview="imagePreview" :current="$category?->imageUrl()" />
            <x-admin.file-input label="Category banner" name="banner" preview="bannerPreview" :current="$category?->bannerUrl()" />
        </div>
    </x-admin.section>

    <x-admin.section title="SEO" description="Search engine metadata for this category page.">
        <div class="grid gap-5">
            <x-admin.input label="Meta title" name="meta_title" :value="old('meta_title', $category?->meta_title)" placeholder="Defaults to category name if empty" />
            <x-admin.textarea label="Meta description" name="meta_description" rows="3" placeholder="Brief description for search results">{{ old('meta_description', $category?->meta_description) }}</x-admin.textarea>
            <x-admin.input label="Meta keywords" name="meta_keywords" :value="old('meta_keywords', $category?->meta_keywords)" placeholder="comma, separated, keywords" />
        </div>
    </x-admin.section>

    <div class="flex flex-wrap items-center gap-3 border-t admin-border pt-6">
        <x-admin.button type="submit">{{ $submitLabel }}</x-admin.button>
        <x-admin.button variant="secondary" :href="route('admin.categories.index')">Cancel</x-admin.button>
    </div>
</form>
