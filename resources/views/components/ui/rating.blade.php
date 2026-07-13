@props([
    'value' => 0,
    'size' => 'md',
])

@php
    $percent = max(0, min(100, ($value / 5) * 100));
    $starSize = $size === 'sm' ? 'size-3.5' : 'size-4.5';
@endphp

<span {{ $attributes->merge(['class' => 'relative inline-flex shrink-0']) }} role="img" aria-label="Rated {{ $value }} out of 5 stars">
    <span class="flex gap-0.5 text-navy-200" aria-hidden="true">
        @for ($i = 0; $i < 5; $i++)
            <svg class="{{ $starSize }}" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.3l-5.8 3.1 1.1-6.5L2.6 9.3l6.5-.9L12 2.5Z"/>
            </svg>
        @endfor
    </span>
    <span class="absolute inset-0 overflow-hidden text-bronze-500" style="width: {{ $percent }}%" aria-hidden="true">
        <span class="flex gap-0.5">
            @for ($i = 0; $i < 5; $i++)
                <svg class="{{ $starSize }} shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.3l-5.8 3.1 1.1-6.5L2.6 9.3l6.5-.9L12 2.5Z"/>
                </svg>
            @endfor
        </span>
    </span>
</span>
