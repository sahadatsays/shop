@php
    $formatDatetimeLocal = fn ($value) => $value ? $value->format('Y-m-d\TH:i') : '';
    $banner ??= null;
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3 dark:bg-red-950/20" role="alert">
        <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
    </div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Content">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input label="Title" name="title" :value="old('title', $banner?->title)" required class="sm:col-span-2" />
                    <x-admin.input label="Subtitle" name="subtitle" :value="old('subtitle', $banner?->subtitle)" class="sm:col-span-2" />
                    <x-admin.textarea label="Description" name="description" rows="4" class="sm:col-span-2">{{ old('description', $banner?->description) }}</x-admin.textarea>
                    <x-admin.input label="Badge text" name="badge_text" :value="old('badge_text', $banner?->badge_text)" class="sm:col-span-2" />
                    <x-admin.input label="Primary button label" name="primary_label" :value="old('primary_label', $banner?->primary_label)" />
                    <x-admin.input label="Primary button URL" name="primary_url" :value="old('primary_url', $banner?->primary_url)" />
                    <x-admin.input label="Secondary button label" name="secondary_label" :value="old('secondary_label', $banner?->secondary_label)" />
                    <x-admin.input label="Secondary button URL" name="secondary_url" :value="old('secondary_url', $banner?->secondary_url)" />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Schedule">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input label="Starts at" name="starts_at" type="datetime-local" :value="old('starts_at', $formatDatetimeLocal($banner?->starts_at))" />
                    <x-admin.input label="Ends at" name="ends_at" type="datetime-local" :value="old('ends_at', $formatDatetimeLocal($banner?->ends_at))" />
                    <x-admin.input label="Sort order" name="sort_order" type="number" min="0" :value="old('sort_order', $banner?->sort_order ?? 0)" />
                    <x-admin.checkbox label="Active" name="is_active" :checked="(bool) old('is_active', $banner?->is_active ?? true)" />
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Images">
                <div class="space-y-5">
                    <x-admin.image-upload label="Desktop image" name="desktop_image" :current="$banner?->desktopImageUrl()" aspect="video" />
                    <x-admin.image-upload label="Mobile image" name="mobile_image" :current="$banner?->mobileImageUrl()" aspect="video" help="Optional. Falls back to desktop image." />
                </div>
            </x-admin.form-card>

            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" class="flex-1">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.homepage.hero-banners.index')" class="flex-1">Cancel</x-admin.button>
            </div>
        </div>
    </div>
</form>
