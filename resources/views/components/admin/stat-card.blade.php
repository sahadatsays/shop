@props([
    'label',
    'value',
    'change' => null,
    'trend' => 'neutral',
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'admin-stat-card rounded-[var(--radius-admin-lg)] border admin-border admin-surface p-5']) }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-medium admin-muted">{{ $label }}</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight admin-text">{{ $value }}</p>
            @if ($change)
                <p @class([
                    'mt-1 flex items-center gap-1 text-xs font-medium',
                    'text-admin-success' => $trend === 'up',
                    'text-admin-danger' => $trend === 'down',
                    'admin-muted' => $trend === 'neutral',
                ])>
                    @if ($trend === 'up')
                        <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                    @elseif ($trend === 'down')
                        <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    @endif
                    {{ $change }}
                </p>
            @endif
        </div>
        @if ($icon)
            <span class="flex size-10 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-admin-brand">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="{{ $icon }}"/></svg>
            </span>
        @endif
    </div>
</div>
