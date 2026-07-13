@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral' => 'bg-navy-100 text-navy-700',
        'navy' => 'bg-navy-900 text-white',
        'olive' => 'bg-olive-100 text-olive-800',
        'bronze' => 'bg-bronze-100 text-bronze-800',
        'success' => 'bg-green-100 text-green-800',
        'danger' => 'bg-red-100 text-red-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold tracking-wide '.($variants[$variant] ?? $variants['neutral'])]) }}>
    {{ $slot }}
</span>
