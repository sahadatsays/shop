@props([
    'lines' => 3,
])

<div {{ $attributes->merge(['class' => 'animate-pulse space-y-3']) }} aria-hidden="true">
    @for ($i = 0; $i < $lines; $i++)
        <div @class([
            'h-4 rounded bg-admin-accent-muted/70',
            'w-full' => $i % 2 === 0,
            'w-4/5' => $i % 2 !== 0,
        ])></div>
    @endfor
</div>
