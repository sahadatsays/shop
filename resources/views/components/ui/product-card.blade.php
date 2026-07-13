@props([
    'name',
    'price',
    'oldPrice' => null,
    'category' => null,
    'badge' => null,
    'badgeVariant' => 'bronze',
    'image' => null,
    'href' => '#',
    'rating' => null,
    'reviews' => null,
    'stock' => null,
    'stockPercent' => null,
])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-card bg-surface shadow-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover']) }}>
    <div class="relative aspect-[4/5] overflow-hidden bg-navy-100">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $name }}" loading="lazy"
                 class="size-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
        @else
            <div class="flex size-full items-center justify-center bg-linear-to-br from-navy-100 via-navy-50 to-bronze-100 transition-transform duration-500 ease-out group-hover:scale-105">
                <svg class="size-14 text-navy-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="9" cy="9" r="2"/><path d="m21 15-4.5-4.5L7 20"/>
                </svg>
            </div>
        @endif

        @if ($badge)
            <div class="pointer-events-none absolute top-3 left-3 z-10">
                <x-ui.badge :variant="$badgeVariant">{{ $badge }}</x-ui.badge>
            </div>
        @endif

        {{-- Hover actions: wishlist + quick view --}}
        <div class="absolute top-3 right-3 z-10 flex flex-col gap-2">
            <button type="button" data-toggle-active aria-label="Add {{ $name }} to wishlist"
                    class="flex size-10 -translate-x-2 items-center justify-center rounded-full bg-white/90 text-navy-700 opacity-0 shadow-soft backdrop-blur-sm transition-all duration-300 hover:bg-navy-900 hover:text-white focus-visible:translate-x-0 focus-visible:opacity-100 group-hover:translate-x-0 group-hover:opacity-100 aria-pressed:text-red-600">
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 21s-7.5-4.7-9.5-9A5.5 5.5 0 0 1 12 6.5 5.5 5.5 0 0 1 21.5 12c-2 4.3-9.5 9-9.5 9Z"/>
                </svg>
            </button>
            <button type="button" aria-label="Quick view {{ $name }}"
                    class="flex size-10 -translate-x-2 items-center justify-center rounded-full bg-white/90 text-navy-700 opacity-0 shadow-soft backdrop-blur-sm transition-all delay-75 duration-300 hover:bg-navy-900 hover:text-white focus-visible:translate-x-0 focus-visible:opacity-100 group-hover:translate-x-0 group-hover:opacity-100">
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 12s3.5-6.5 10-6.5S22 12 22 12s-3.5 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/>
                </svg>
            </button>
        </div>

        {{-- Slide-up add to cart --}}
        <div class="absolute inset-x-3 bottom-3 z-10">
            <button type="button"
                    class="flex w-full translate-y-3 items-center justify-center gap-2 rounded-xl bg-navy-900/90 px-4 py-3 text-sm font-semibold text-white opacity-0 shadow-card backdrop-blur-sm transition-all duration-300 hover:bg-navy-800 focus-visible:translate-y-0 focus-visible:opacity-100 group-hover:translate-y-0 group-hover:opacity-100">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                </svg>
                Add to cart
            </button>
        </div>
    </div>

    <div class="flex flex-1 flex-col gap-1.5 p-5">
        @if ($category)
            <p class="text-xs font-medium tracking-wide text-navy-500 uppercase">{{ $category }}</p>
        @endif

        <h3 class="font-display text-base font-semibold text-navy-900">
            <a href="{{ $href }}" class="after:absolute after:inset-0">{{ $name }}</a>
        </h3>

        @if ($rating !== null)
            <div class="flex items-center gap-2">
                <x-ui.rating :value="$rating" size="sm" />
                <span class="text-xs text-navy-500">{{ $rating }}@if ($reviews !== null) ({{ $reviews }})@endif</span>
            </div>
        @endif

        <p class="mt-auto flex items-baseline gap-2 pt-2">
            <span class="text-base font-bold text-navy-900">{{ $price }}</span>
            @if ($oldPrice)
                <span class="text-sm text-navy-400 line-through">{{ $oldPrice }}</span>
            @endif
        </p>

        @if ($stock)
            <div class="mt-1">
                @if ($stockPercent !== null)
                    <div class="h-1.5 overflow-hidden rounded-full bg-navy-100">
                        <div class="h-full rounded-full {{ $stockPercent <= 25 ? 'bg-red-500' : 'bg-olive-600' }}" style="width: {{ $stockPercent }}%"></div>
                    </div>
                @endif
                <p class="mt-1.5 text-xs font-medium {{ $stockPercent !== null && $stockPercent <= 25 ? 'text-red-600' : 'text-olive-700' }}">{{ $stock }}</p>
            </div>
        @endif
    </div>
</article>
