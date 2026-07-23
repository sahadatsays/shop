@props([
    'variant' => 'default',
    'dot' => false,
])

@php
    $styles = match ($variant) {
        'success' => ['badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
        'warning' => ['badge' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400', 'dot' => 'bg-amber-500'],
        'danger' => ['badge' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400', 'dot' => 'bg-red-500'],
        'info' => ['badge' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', 'dot' => 'bg-blue-500'],
        'brand' => ['badge' => 'bg-admin-brand/10 text-admin-brand', 'dot' => 'bg-admin-brand'],
        'muted' => ['badge' => 'bg-slate-100 text-slate-600 dark:bg-slate-500/10 dark:text-slate-400', 'dot' => 'bg-slate-400'],
        default => ['badge' => 'bg-slate-100 text-slate-600 dark:bg-slate-500/10 dark:text-slate-400', 'dot' => 'bg-slate-400'],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {$styles['badge']}"]) }}>
    @if ($dot)
        <span class="size-1.5 rounded-full {{ $styles['dot'] }}"></span>
    @endif
    {{ $slot }}
</span>
