@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $classes = match ($variant) {
        'secondary' => 'border admin-border admin-surface admin-text-secondary hover:bg-admin-accent-muted/60',
        'ghost' => 'admin-text-secondary hover:bg-admin-accent-muted/60',
        'danger' => 'bg-admin-danger text-white hover:bg-red-700',
        default => 'bg-admin-accent text-white hover:bg-admin-accent-hover dark:text-admin-bg',
    };

    $sizeClass = $size === 'sm' ? 'px-3 py-1.5 text-xs' : 'px-4 py-2 text-sm';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-[var(--radius-admin)] font-medium admin-focus-ring transition-all duration-150 active:scale-[0.97] {$classes} {$sizeClass}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-[var(--radius-admin)] font-medium admin-focus-ring transition-all duration-150 active:scale-[0.97] {$classes} {$sizeClass}"]) }}>
        {{ $slot }}
    </button>
@endif
