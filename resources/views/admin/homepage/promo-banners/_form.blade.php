@php $banner ??= null; @endphp

@if ($errors->any())
    <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3" role="alert">
        <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
    </div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf @if ($method !== 'POST') @method($method) @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Promo banner">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.select label="Layout" name="layout" required class="sm:col-span-2">
                        @foreach (\App\Enums\PromoBannerLayout::cases() as $layout)
                            <option value="{{ $layout->value }}" @selected(old('layout', $banner?->layout?->value) === $layout->value)>{{ $layout->label() }}</option>
                        @endforeach
                    </x-admin.select>
                    <x-admin.input label="Title" name="title" :value="old('title', $banner?->title)" required class="sm:col-span-2" />
                    <x-admin.input label="Button label" name="button_label" :value="old('button_label', $banner?->button_label)" />
                    <x-admin.input label="URL" name="url" :value="old('url', $banner?->url)" />
                    <x-admin.input label="Sort order" name="sort_order" type="number" min="0" :value="old('sort_order', $banner?->sort_order ?? 0)" />
                    <x-admin.checkbox label="Active" name="is_active" :checked="(bool) old('is_active', $banner?->is_active ?? true)" />
                </div>
            </x-admin.form-card>
        </div>
        <div class="space-y-6">
            <x-admin.form-card title="Image">
                <x-admin.image-upload label="Banner image" name="image" :current="$banner?->imageUrl()" aspect="video" />
            </x-admin.form-card>
            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" class="flex-1">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.homepage.promo-banners.index')" class="flex-1">Cancel</x-admin.button>
            </div>
        </div>
    </div>
</form>
