@props([
    'label' => null,
    'name' => null,
])

<label class="block">
    @if ($label)
        <span class="mb-1.5 block text-sm font-medium admin-text">{{ $label }}</span>
    @endif
    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3 py-2 text-sm admin-text admin-focus-ring']) }}>
        {{ $slot }}
    </select>
</label>
