@props([
    'label' => null,
    'name',
    'help' => null,
    'required' => false,
])

@php
    $hasError = $errors->has($name);
    $inputId = $attributes->get('id', 'field-'.$name);
@endphp

<div {{ $attributes->class('space-y-1.5') }}>
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium admin-text">
            {{ $label }}
            @if ($required)
                <span class="text-admin-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if ($hasError)
        <p class="text-xs text-admin-danger" role="alert" id="{{ $inputId }}-error">{{ $errors->first($name) }}</p>
    @elseif ($help)
        <p class="text-xs admin-muted" id="{{ $inputId }}-help">{{ $help }}</p>
    @endif
</div>
