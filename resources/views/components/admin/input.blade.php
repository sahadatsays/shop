@props([
    'label' => null,
    'name',
    'type' => 'text',
    'help' => null,
    'required' => false,
    'value' => null,
])

@php
    $hasError = $errors->has($name);
    $inputId = 'field-'.$name;
    $wrapperClass = $attributes->get('class');
@endphp

<x-admin.field :label="$label" :name="$name" :help="$help" :required="$required" :class="$wrapperClass">
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ old($name, $value) }}"
        @if ($required) required @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @elseif ($help) aria-describedby="{{ $inputId }}-help" @endif
        {{ $attributes->except('class')->merge([
            'class' => 'block w-full rounded-[var(--radius-admin)] border bg-admin-bg px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring '.($hasError ? 'border-admin-danger' : 'admin-border'),
        ]) }}
    >
</x-admin.field>
