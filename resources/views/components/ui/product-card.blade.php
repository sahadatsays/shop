@props([
    'name',
    'price',
    'category' => null,
    'badge' => null,
    'badgeVariant' => 'bronze',
    'image' => null,
    'href' => '#',
])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-card bg-surface shadow-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover']) }}>
    <div class="relative aspect-[4/5] overflow-hidden bg-navy-100">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $name }}" loading="lazy"
                 class="size-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
        @else
            <div class="flex size-full items-center justify-center bg-gradient-to-br from-navy-100 via-navy-50 to-bronze-100 transition-transform duration-500 ease-out group-hover:scale-105">
                <svg class="size-14 text-navy-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="9" cy="9" r="2"/><path d="m21 15-4.5-4.5L7 20"/>
                </svg>
            </div>
        @endif

        @if ($badge)
            <div class="absolute top-3 left-3">
                <x-ui.badge :variant="$badgeVariant">{{ $badge }}</x-ui.badge>
            </div>
        @endif

        <button type="button" aria-label="Add {{ $name }} to cart"
                class="absolute right-3 bottom-3 flex size-11 translate-y-2 items-center justify-center rounded-full bg-navy-900 text-white opacity-0 shadow-card transition-all duration-300 hover:bg-navy-800 focus-visible:translate-y-0 focus-visible:opacity-100 group-hover:translate-y-0 group-hover:opacity-100">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                <path d="M12 5v14M5 12h14"/>
            </svg>
        </button>
    </div>

    <div class="flex flex-1 flex-col gap-1 p-5">
        @if ($category)
            <p class="text-xs font-medium tracking-wide text-navy-500 uppercase">{{ $category }}</p>
        @endif
        <h3 class="font-display text-base font-semibold text-navy-900">
            <a href="{{ $href }}" class="after:absolute after:inset-0">{{ $name }}</a>
        </h3>
        <p class="mt-auto pt-2 text-base font-bold text-navy-900">{{ $price }}</p>
    </div>
</article>
