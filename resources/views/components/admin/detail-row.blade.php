@props([
    'label',
    'value' => null,
])

<div {{ $attributes->class('grid gap-1 border-b admin-border py-3 last:border-b-0 sm:grid-cols-[10rem_minmax(0,1fr)] sm:gap-4') }}>
    <dt class="text-xs font-medium uppercase tracking-wide admin-muted sm:pt-0.5">{{ $label }}</dt>
    <dd class="min-w-0 text-sm admin-text">
        @if (! $slot->isEmpty())
            {{ $slot }}
        @elseif (filled($value) || $value === 0 || $value === '0')
            {{ $value }}
        @else
            <span class="admin-muted">—</span>
        @endif
    </dd>
</div>
