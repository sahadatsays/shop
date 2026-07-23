@props([
    'label',
    'name',
    'value' => '1',
    'checked' => false,
    'help' => null,
])

@php
    $hasError = $errors->has($name);
@endphp

<div {{ $attributes->class('space-y-1.5') }}>
    <label class="inline-flex cursor-pointer items-start gap-3">
        <input
            type="checkbox"
            name="{{ $name }}"
            value="{{ $value }}"
            @checked(is_array(old(str_replace('[]', '', $name))) ? in_array($value, old(str_replace('[]', '', $name), []), true) : old($name, $checked))
            class="mt-0.5 size-4 rounded border admin-border text-admin-accent admin-focus-ring"
        >
        <span>
            <span class="block text-sm font-medium admin-text">{{ $label }}</span>
            @if ($help)
                <span class="mt-0.5 block text-xs admin-muted">{{ $help }}</span>
            @endif
        </span>
    </label>
    @if ($hasError)
        <p class="text-xs text-admin-danger" role="alert">{{ $errors->first($name) }}</p>
    @endif
</div>
