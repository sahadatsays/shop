@php
    $category = $form->category;
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
        'name' => old('name', $category?->name ?? ''),
        'slug' => old('slug', $category?->slug ?? ''),
        'autoSlug' => ! $category && ! old('slug'),
        'imageUrl' => $category?->imageUrl(),
        'bannerUrl' => $category?->bannerUrl(),
    ]))"
    class="space-y-6"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Basic information" description="Name, URL slug, hierarchy, and display order.">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Name"
                        name="name"
                        x-model="name"
                        @input="onNameInput"
                        :value="old('name', $category?->name)"
                        placeholder="e.g. Outdoor Gear"
                        help="Display name shown in menus and filters."
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
                            value="{{ old('slug', $category?->slug) }}"
                            placeholder="outdoor-gear"
                            @class([
                                'block w-full rounded-[var(--radius-admin)] border bg-admin-bg px-3.5 py-2.5 font-mono text-sm admin-text placeholder:admin-muted admin-focus-ring',
                                'border-admin-danger' => $errors->has('slug'),
                                'admin-border' => ! $errors->has('slug'),
                            ])
                        >
                    </x-admin.field>

                    <x-admin.select label="Parent category" name="parent_id" help="Leave empty for a top-level category.">
                        <option value="">None (top-level)</option>
                        @foreach ($form->parentOptions as $parent)
                            <option value="{{ $parent->id }}" @selected(old('parent_id', $category?->parent_id) == $parent->id)>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.select label="Status" name="status" help="Inactive categories stay hidden on the storefront." required>
                        @foreach (\App\Enums\CategoryStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $category?->status?->value ?? 'active') === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.input
                        label="Sort order"
                        name="sort_order"
                        type="number"
                        min="0"
                        :value="old('sort_order', $category?->sort_order ?? 0)"
                        placeholder="0"
                        help="Lower numbers appear first."
                    />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="SEO" description="Search engine metadata for this category page.">
                <div class="space-y-5">
                    <x-admin.input
                        label="Meta title"
                        name="meta_title"
                        :value="old('meta_title', $category?->meta_title)"
                        placeholder="Leave blank to use the category name"
                        help="Recommended under 60 characters."
                    />
                    <x-admin.textarea
                        label="Meta description"
                        name="meta_description"
                        rows="3"
                        placeholder="A short summary for search results…"
                        help="Recommended under 160 characters."
                    >{{ old('meta_description', $category?->meta_description) }}</x-admin.textarea>
                    <x-admin.input
                        label="Meta keywords"
                        name="meta_keywords"
                        :value="old('meta_keywords', $category?->meta_keywords)"
                        placeholder="veteran, outdoor, apparel"
                        help="Comma-separated keywords."
                    />
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Media" description="Thumbnail and banner for storefront display.">
                <div class="space-y-5">
                    <x-admin.image-upload
                        label="Category image"
                        name="image"
                        preview="imagePreview"
                        :current="$category?->imageUrl()"
                        aspect="square"
                        help="Square crop works best. Max 2MB."
                    />
                    <x-admin.image-upload
                        label="Category banner"
                        name="banner"
                        preview="bannerPreview"
                        :current="$category?->bannerUrl()"
                        aspect="wide"
                        help="Wide banner for category pages. Max 4MB."
                    />
                </div>
            </x-admin.form-card>

            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" class="flex-1">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.categories.index')" class="flex-1">Cancel</x-admin.button>
            </div>
        </div>
    </div>
</form>
