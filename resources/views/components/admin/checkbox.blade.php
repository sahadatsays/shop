@props([
    'label',
    'name',
    'checked' => false,
])

<label class="inline-flex items-center gap-2 text-sm admin-text-secondary">
    <input type="checkbox" name="{{ $name }}" @checked($checked) {{ $attributes->merge(['class' => 'size-4 rounded border admin-border text-admin-accent admin-focus-ring']) }}>
    <span>{{ $label }}</span>
</label>
