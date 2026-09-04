@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
])

@php
    $base = 'inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl font-semibold transition-all duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bronze-500 disabled:cursor-not-allowed disabled:pointer-events-none disabled:opacity-50';

    $variants = [
        'primary' => 'bg-[var(--store-button-primary-bg,var(--color-navy-900))] text-[var(--store-button-primary-text,white)] shadow-soft hover:opacity-90 hover:shadow-card active:scale-[0.98]',
        'secondary' => 'bg-olive-600 text-white shadow-soft hover:bg-olive-700 hover:shadow-card active:scale-[0.98]',
        'accent' => 'bg-[var(--store-button-accent-bg,var(--color-bronze-500))] text-[var(--store-button-accent-text,white)] shadow-soft hover:opacity-90 hover:shadow-card active:scale-[0.98]',
        'outline' => 'border border-navy-200 bg-surface text-navy-900 hover:border-navy-300 hover:bg-navy-50 active:scale-[0.98]',
        'ghost' => 'text-navy-700 hover:bg-navy-900/5 hover:text-navy-900',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-sm',
        'lg' => 'px-8 py-4 text-base',
    ];

    $classes = $base.' '.($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
