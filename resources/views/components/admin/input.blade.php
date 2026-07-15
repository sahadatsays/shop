@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
])

<label class="block">
    @if ($label)
        <span class="mb-1.5 block text-sm font-medium admin-text">{{ $label }}</span>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3 py-2 text-sm admin-text placeholder:admin-muted admin-focus-ring']) }}>
</label>
