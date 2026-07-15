@props([
    'variant' => 'default',
])

@php
    $classes = match ($variant) {
        'success' => 'bg-emerald-50 text-admin-success ring-1 ring-emerald-200 dark:bg-emerald-950/40 dark:ring-emerald-900',
        'warning' => 'bg-amber-50 text-admin-warning ring-1 ring-amber-200 dark:bg-amber-950/40 dark:ring-amber-900',
        'danger' => 'bg-red-50 text-admin-danger ring-1 ring-red-200 dark:bg-red-950/40 dark:ring-red-900',
        'brand' => 'bg-admin-accent-muted text-admin-brand ring-1 ring-admin-border',
        'muted' => 'bg-admin-accent-muted/60 admin-muted ring-1 ring-admin-border',
        default => 'bg-admin-accent-muted admin-text-secondary ring-1 ring-admin-border',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {$classes}"]) }}>
    {{ $slot }}
</span>
