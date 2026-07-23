@props([
    'name',
    'href' => '#',
    'logo' => null,
])

<li {{ $attributes }}>
    <a href="{{ $href }}" class="font-display text-lg font-bold tracking-wide text-navy-300 transition-colors duration-300 hover:text-navy-600" aria-label="{{ $name }}">
        @if ($logo)
            <img src="{{ $logo }}" alt="{{ $name }}" loading="lazy" class="mx-auto h-10 w-auto object-contain opacity-70 transition hover:opacity-100">
        @else
            {{ $name }}
        @endif
    </a>
</li>
