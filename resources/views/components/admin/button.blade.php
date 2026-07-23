@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'icon' => false,
])

@php
    $classes = match ($variant) {
        'secondary' => 'border admin-border bg-admin-surface admin-text-secondary hover:bg-admin-accent-muted/50 hover:admin-text',
        'ghost' => 'admin-text-secondary hover:bg-admin-accent-muted/60 hover:admin-text',
        'danger' => 'bg-admin-danger text-white hover:bg-red-700 shadow-sm shadow-red-500/20',
        'danger-ghost' => 'text-admin-danger hover:bg-red-50 dark:hover:bg-red-950/30',
        'soft' => 'bg-admin-accent-muted admin-text hover:bg-admin-accent-muted/80',
        default => 'bg-admin-accent text-white hover:bg-admin-accent-hover shadow-sm shadow-slate-900/10 dark:text-admin-bg',
    };

    $sizeClass = match ($size) {
        'xs' => 'h-8 gap-1.5 px-2.5 text-xs',
        'sm' => 'h-9 gap-1.5 px-3 text-xs',
        'lg' => 'h-11 gap-2 px-5 text-sm',
        'icon' => 'size-9 p-0',
        'icon-sm' => 'size-8 p-0',
        default => 'h-10 gap-2 px-4 text-sm',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-[var(--radius-admin)] font-medium whitespace-nowrap admin-focus-ring transition-all duration-150 active:scale-[0.98] {$classes} {$sizeClass}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-[var(--radius-admin)] font-medium whitespace-nowrap admin-focus-ring transition-all duration-150 active:scale-[0.98] {$classes} {$sizeClass}"]) }}>
        {{ $slot }}
    </button>
@endif
