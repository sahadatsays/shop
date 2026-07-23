@props([
    'label',
    'name',
    'accept' => 'image/*',
    'preview' => null,
    'current' => null,
])

<label class="block">
    <span class="mb-1.5 block text-sm font-medium admin-text">{{ $label }}</span>
    @if ($current)
        <div class="mb-2 overflow-hidden rounded-[var(--radius-admin)] border admin-border">
            <img src="{{ $current }}" alt="" class="h-32 w-full object-cover">
        </div>
    @endif
    @if ($preview)
        <div x-show="{{ $preview }}" x-cloak class="mb-2 overflow-hidden rounded-[var(--radius-admin)] border admin-border">
            <img :src="{{ $preview }}" alt="" class="h-32 w-full object-cover">
        </div>
    @endif
    <input
        type="file"
        name="{{ $name }}"
        accept="{{ $accept }}"
        @if ($preview) @change="previewFile($event, '{{ $preview }}')" @endif
        {{ $attributes->merge(['class' => 'block w-full text-sm admin-text-secondary file:mr-3 file:rounded-[var(--radius-admin)] file:border-0 file:bg-admin-accent-muted file:px-3 file:py-2 file:text-sm file:font-medium file:admin-text admin-focus-ring']) }}
    >
</label>
