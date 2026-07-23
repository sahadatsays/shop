@props([
    'name',
    'price',
    'oldPrice' => null,
    'category' => null,
    'brand' => null,
    'shortDescription' => null,
    'badge' => null,
    'badgeVariant' => 'bronze',
    'image' => null,
    'href' => '#',
    'rating' => null,
    'reviews' => null,
    'stock' => null,
    'stockPercent' => null,
    'productId' => null,
])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col gap-4 overflow-hidden rounded-card bg-surface p-4 shadow-card transition-all duration-300 ease-out hover:shadow-card-hover sm:flex-row sm:items-center sm:gap-6 sm:p-5']) }}>
    <div class="relative aspect-4/5 w-full shrink-0 overflow-hidden rounded-xl bg-navy-100 sm:aspect-square sm:w-44 lg:w-52">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $name }}" loading="lazy"
                 class="size-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
        @else
            <div class="flex size-full items-center justify-center bg-linear-to-br from-navy-100 via-navy-50 to-bronze-100">
                <svg class="size-14 text-navy-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                </svg>
            </div>
        @endif

        @if ($badge)
            <div class="pointer-events-none absolute top-3 left-3 z-10">
                <x-ui.badge :variant="$badgeVariant">{{ $badge }}</x-ui.badge>
            </div>
        @endif
    </div>

    <div class="flex min-w-0 flex-1 flex-col gap-3">
        <div>
            @if ($category || $brand)
                <p class="text-xs font-medium tracking-wide text-navy-500 uppercase">
                    @if ($category)
                        {{ $category }}
                    @endif
                    @if ($category && $brand)
                        <span class="text-navy-300"> · </span>
                    @endif
                    @if ($brand)
                        {{ $brand }}
                    @endif
                </p>
            @endif

            <h3 class="mt-1 font-display text-lg font-semibold text-navy-900">
                <a href="{{ $href }}" class="transition-colors duration-200 hover:text-olive-700">{{ $name }}</a>
            </h3>

            @if ($shortDescription)
                <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-navy-600">{{ $shortDescription }}</p>
            @endif
        </div>

        @if ($rating !== null)
            <div class="flex items-center gap-2">
                <x-ui.rating :value="$rating" size="sm" />
                <span class="text-xs text-navy-500">{{ $rating }}@if ($reviews !== null) ({{ $reviews }})@endif</span>
            </div>
        @endif

        <div class="mt-auto flex flex-wrap items-end justify-between gap-4 pt-2">
            <div>
                <p class="flex items-baseline gap-2">
                    <span class="text-xl font-bold text-navy-900">{{ $price }}</span>
                    @if ($oldPrice)
                        <span class="text-sm text-navy-400 line-through">{{ $oldPrice }}</span>
                    @endif
                </p>

                @if ($stock)
                    <div class="mt-2 max-w-xs">
                        @if ($stockPercent !== null)
                            <div class="h-1.5 overflow-hidden rounded-full bg-navy-100">
                                <div class="h-full rounded-full {{ $stockPercent <= 25 ? 'bg-red-500' : 'bg-olive-600' }}" style="width: {{ $stockPercent }}%"></div>
                            </div>
                        @endif
                        <p class="mt-1.5 text-xs font-medium {{ $stockPercent !== null && $stockPercent <= 25 ? 'text-red-600' : 'text-olive-700' }}">{{ $stock }}</p>
                    </div>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" data-toggle-active aria-label="Add {{ $name }} to wishlist"
                        class="flex size-10 items-center justify-center rounded-xl border border-navy-200 text-navy-600 transition-colors duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white">
                    <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="M12 21s-7.5-4.7-9.5-9A5.5 5.5 0 0 1 12 6.5 5.5 5.5 0 0 1 21.5 12c-2 4.3-9.5 9-9.5 9Z"/>
                    </svg>
                </button>
                <button type="button" aria-label="Quick view {{ $name }}"
                        class="flex size-10 items-center justify-center rounded-xl border border-navy-200 text-navy-600 transition-colors duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white">
                    <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="M2 12s3.5-6.5 10-6.5S22 12 22 12s-3.5 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/>
                    </svg>
                </button>
                <x-ui.button :href="$href" variant="outline" size="sm">View details</x-ui.button>
                <button type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-bronze-500 px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-bronze-600"
                        @if ($productId)
                            data-add-to-cart
                            data-product-id="{{ $productId }}"
                        @endif>
                    Add to cart
                </button>
            </div>
        </div>
    </div>
</article>
