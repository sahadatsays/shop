@props([
    'label' => null,
    'name',
    'help' => null,
    'required' => false,
])

@php
    $hasError = $errors->has($name);
    $inputId = 'field-'.$name;
    $wrapperClass = $attributes->get('class');
@endphp

<x-admin.field :label="$label" :name="$name" :help="$help" :required="$required" :class="$wrapperClass">
    <select
        name="{{ $name }}"
        id="{{ $inputId }}"
        @if ($required) required @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @elseif ($help) aria-describedby="{{ $inputId }}-help" @endif
        {{ $attributes->except('class')->merge([
            'class' => 'block w-full rounded-[var(--radius-admin)] border bg-admin-bg px-3.5 py-2.5 text-sm admin-text admin-focus-ring '.($hasError ? 'border-admin-danger' : 'admin-border'),
        ]) }}
    >
        {{ $slot }}
    </select>
</x-admin.field>
