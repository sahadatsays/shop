@props([
    'name',
    'size' => 'md',
])

@php
    $initials = collect(explode(' ', $name))->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->join('');
    $sizeClass = $size === 'sm' ? 'size-7 text-[10px]' : 'size-8 text-xs';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center rounded-full bg-admin-accent-muted font-semibold admin-text {$sizeClass}"]) }} aria-hidden="true">
    {{ $initials }}
</span>
