@props([
    'label',
    'id',
])

<div class="relative inline-block">
    <button type="button"
            data-panel-trigger
            aria-controls="{{ $id }}"
            aria-expanded="false"
            aria-haspopup="true"
            {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-[var(--radius-admin)] border admin-border px-3 py-2 text-sm font-medium admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60']) }}>
        {{ $label }}
        <svg class="size-4 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
    </button>
    <div id="{{ $id }}" data-admin-panel hidden role="menu" class="absolute right-0 z-50 mt-2 min-w-40 rounded-[var(--radius-admin-lg)] border admin-border admin-surface p-1 shadow-lg">
        {{ $slot }}
    </div>
</div>
