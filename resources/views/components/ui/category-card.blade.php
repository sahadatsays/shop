@props([
    'name',
    'count' => 0,
    'href' => '#',
    'icon' => 'M3 11 12 3l9 8M6 10v10h12V10',
    'image' => null,
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group flex flex-col items-start gap-4 rounded-card bg-surface p-6 shadow-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover sm:p-8']) }}>
    @if ($image)
        <span class="flex size-14 items-center justify-center overflow-hidden rounded-2xl bg-olive-100">
            <img src="{{ $image }}" alt="" loading="lazy" class="size-full object-cover">
        </span>
    @else
        <span class="flex size-14 items-center justify-center rounded-2xl bg-olive-100 text-olive-700 transition-all duration-300 group-hover:scale-110 group-hover:bg-olive-600 group-hover:text-white">
            <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="{{ $icon }}"/>
            </svg>
        </span>
    @endif
    <span>
        <span class="block font-display text-base font-bold text-navy-900">{{ $name }}</span>
        <span class="mt-1 block text-sm text-navy-500">{{ $count }} {{ $count === 1 ? 'product' : 'products' }}</span>
    </span>
    <span class="mt-auto flex items-center gap-1.5 text-sm font-semibold text-bronze-600 opacity-0 transition-all duration-300 group-hover:opacity-100">
        Shop now
        <svg class="size-3.5 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
    </span>
</a>
