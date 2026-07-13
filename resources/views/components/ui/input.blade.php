@props([
    'label' => null,
    'name',
    'type' => 'text',
    'error' => null,
    'hint' => null,
])

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-navy-900">{{ $label }}</label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        @if ($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @elseif ($hint) aria-describedby="{{ $name }}-hint" @endif
        {{ $attributes->merge([
            'class' => 'block w-full rounded-field border bg-surface px-4 py-3 text-sm text-ink placeholder:text-navy-400 shadow-soft transition-colors duration-200 focus:outline-2 focus:outline-offset-2 '
                .($error
                    ? 'border-red-300 focus:outline-red-500'
                    : 'border-navy-200 hover:border-navy-300 focus:outline-bronze-500'),
        ]) }}
    >

    @if ($error)
        <p id="{{ $name }}-error" class="text-sm text-red-600">{{ $error }}</p>
    @elseif ($hint)
        <p id="{{ $name }}-hint" class="text-sm text-navy-500">{{ $hint }}</p>
    @endif
</div>
