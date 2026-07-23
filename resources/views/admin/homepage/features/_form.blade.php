@php $feature ??= null; @endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf @if ($method !== 'POST') @method($method) @endif
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Feature">
                <div class="space-y-5">
                    <x-admin.textarea label="Icon path (SVG path data)" name="icon" rows="3" required>{{ old('icon', $feature?->icon) }}</x-admin.textarea>
                    <x-admin.input label="Title" name="title" :value="old('title', $feature?->title)" required />
                    <x-admin.textarea label="Description" name="description" rows="3">{{ old('description', $feature?->description) }}</x-admin.textarea>
                    <x-admin.input label="Sort order" name="sort_order" type="number" min="0" :value="old('sort_order', $feature?->sort_order ?? 0)" />
                    <x-admin.checkbox label="Active" name="is_active" :checked="(bool) old('is_active', $feature?->is_active ?? true)" />
                </div>
            </x-admin.form-card>
        </div>
        <div class="flex flex-col gap-2">
            <x-admin.button type="submit" class="w-full">{{ $submitLabel }}</x-admin.button>
            <x-admin.button variant="secondary" :href="route('admin.homepage.features.index')" class="w-full">Cancel</x-admin.button>
        </div>
    </div>
</form>
