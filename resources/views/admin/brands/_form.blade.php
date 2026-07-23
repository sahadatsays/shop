@php
    $brand = $form->brand;
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
        'name' => old('name', $brand?->name ?? ''),
        'slug' => old('slug', $brand?->slug ?? ''),
        'autoSlug' => ! $brand && ! old('slug'),
        'imageUrl' => $brand?->logoUrl(),
    ]))"
    class="space-y-6"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Basic information" description="Brand identity, status, and featured placement.">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Name"
                        name="name"
                        x-model="name"
                        @input="onNameInput"
                        :value="old('name', $brand?->name)"
                        placeholder="e.g. Valor Outfitters"
                        help="Display name shown on product pages and filters."
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
                            value="{{ old('slug', $brand?->slug) }}"
                            placeholder="valor-outfitters"
                            @class([
                                'block w-full rounded-[var(--radius-admin)] border bg-admin-bg px-3.5 py-2.5 font-mono text-sm admin-text placeholder:admin-muted admin-focus-ring',
                                'border-admin-danger' => $errors->has('slug'),
                                'admin-border' => ! $errors->has('slug'),
                            ])
                        >
                    </x-admin.field>

                    <x-admin.select label="Status" name="status" help="Inactive brands stay hidden on the storefront." required>
                        @foreach (\App\Enums\BrandStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $brand?->status?->value ?? 'active') === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.input
                        label="Sort order"
                        name="sort_order"
                        type="number"
                        min="0"
                        :value="old('sort_order', $brand?->sort_order ?? 0)"
                        placeholder="0"
                        help="Lower numbers appear first."
                    />

                    <x-admin.checkbox
                        label="Featured brand"
                        name="is_featured"
                        :checked="(bool) old('is_featured', $brand?->is_featured)"
                        help="Featured brands appear on the admin dashboard and storefront highlights."
                        class="sm:col-span-2"
                    />

                    <x-admin.textarea
                        label="Description"
                        name="description"
                        rows="4"
                        placeholder="Brand story and positioning…"
                        help="Optional long-form description for the brand page."
                        class="sm:col-span-2"
                    >{{ old('description', $brand?->description) }}</x-admin.textarea>
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="SEO" description="Search engine metadata for this brand page.">
                <div class="space-y-5">
                    <x-admin.input
                        label="Meta title"
                        name="meta_title"
                        :value="old('meta_title', $brand?->meta_title)"
                        placeholder="Leave blank to use the brand name"
                        help="Recommended under 60 characters."
                    />
                    <x-admin.textarea
                        label="Meta description"
                        name="meta_description"
                        rows="3"
                        placeholder="A short summary for search results…"
                        help="Recommended under 160 characters."
                    >{{ old('meta_description', $brand?->meta_description) }}</x-admin.textarea>
                    <x-admin.input
                        label="Meta keywords"
                        name="meta_keywords"
                        :value="old('meta_keywords', $brand?->meta_keywords)"
                        placeholder="veteran, outdoor, apparel"
                        help="Comma-separated keywords."
                    />
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Logo" description="Brand mark for listings and featured widgets.">
                <x-admin.image-upload
                    label="Brand logo"
                    name="logo"
                    preview="imagePreview"
                    :current="$brand?->logoUrl()"
                    aspect="square"
                    help="Square logo works best. PNG or WEBP preferred. Max 2MB."
                />
            </x-admin.form-card>

            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" class="flex-1">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.brands.index')" class="flex-1">Cancel</x-admin.button>
            </div>
        </div>
    </div>
</form>
