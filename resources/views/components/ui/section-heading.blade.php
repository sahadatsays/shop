@props([
    'eyebrow' => null,
    'title',
    'subtitle' => null,
    'align' => 'center',
])

<div {{ $attributes->merge(['class' => 'max-w-2xl '.($align === 'center' ? 'mx-auto text-center' : '')]) }}>
    @if ($eyebrow)
        <p class="text-sm font-semibold tracking-widest text-bronze-600 uppercase">{{ $eyebrow }}</p>
    @endif
    <h2 class="mt-3 font-display text-3xl font-bold text-navy-900 sm:text-4xl">{{ $title }}</h2>
    @if ($subtitle)
        <p class="mt-4 text-lg leading-relaxed text-navy-600">{{ $subtitle }}</p>
    @endif
</div>
