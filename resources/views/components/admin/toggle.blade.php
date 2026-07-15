@props([
    'label',
    'name',
    'checked' => false,
])

<label class="inline-flex items-center gap-3 text-sm admin-text-secondary">
    <span>{{ $label }}</span>
    <span class="relative inline-flex">
        <input type="checkbox" name="{{ $name }}" role="switch" @checked($checked) {{ $attributes->merge(['class' => 'peer sr-only']) }}>
        <span class="h-6 w-10 rounded-full bg-admin-accent-muted transition peer-checked:bg-admin-accent peer-focus-visible:ring-2 peer-focus-visible:ring-admin-accent/40"></span>
        <span class="absolute left-0.5 top-0.5 size-5 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
    </span>
</label>
