@php
    $results = [
        ['name' => 'Ranger Field Jacket', 'category' => 'Apparel', 'price' => '$189.00', 'oldPrice' => '$249.00', 'badge' => '-24%', 'badgeVariant' => 'danger', 'rating' => 4.8, 'reviews' => 132, 'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Patriot Canvas Rucksack', 'category' => 'Outdoor Gear', 'price' => '$149.00', 'oldPrice' => null, 'badge' => 'Best seller', 'badgeVariant' => 'bronze', 'rating' => 4.9, 'reviews' => 87, 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Sentinel Field Watch', 'category' => 'Accessories', 'price' => '$229.00', 'oldPrice' => '$279.00', 'badge' => '-18%', 'badgeVariant' => 'danger', 'rating' => 4.7, 'reviews' => 64, 'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Garrison Heritage Tee', 'category' => 'Apparel', 'price' => '$38.00', 'oldPrice' => null, 'badge' => 'New', 'badgeVariant' => 'olive', 'rating' => 4.6, 'reviews' => 53, 'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Honor EDC Kit', 'category' => 'Everyday Carry', 'price' => '$96.00', 'oldPrice' => '$120.00', 'badge' => '-20%', 'badgeVariant' => 'danger', 'rating' => 4.6, 'reviews' => 41, 'image' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Anniversary Stitched Flag', 'category' => 'Flags', 'price' => '$120.00', 'oldPrice' => null, 'badge' => 'Limited', 'badgeVariant' => 'navy', 'rating' => 5.0, 'reviews' => 28, 'image' => 'https://images.unsplash.com/photo-1520095972714-909e91b038e5?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Field Manual Collection', 'category' => 'Books', 'price' => '$54.00', 'oldPrice' => '$68.00', 'badge' => '-21%', 'badgeVariant' => 'danger', 'rating' => 4.5, 'reviews' => 36, 'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Trail Proven Boots', 'category' => 'Outdoor Gear', 'price' => '$210.00', 'oldPrice' => null, 'badge' => null, 'badgeVariant' => 'bronze', 'rating' => 4.7, 'reviews' => 72, 'image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&q=70&auto=format&fit=crop'],
    ];

    $liveFilterCategories = ['Apparel', 'Outdoor Gear', 'Accessories', 'Everyday Carry', 'Flags', 'Books'];

    $trendingProducts = [
        ['name' => 'Founder\'s Challenge Coin', 'category' => 'Challenge Coins', 'price' => '$65.00', 'rating' => 4.9, 'reviews' => 44, 'image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Heritage Automatic Watch', 'category' => 'Accessories', 'price' => '$449.00', 'rating' => 4.9, 'reviews' => 19, 'image' => 'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Everyday Leather Wallet', 'category' => 'Everyday Carry', 'price' => '$79.00', 'rating' => 4.8, 'reviews' => 96, 'image' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Basecamp Reading Set', 'category' => 'Books', 'price' => '$48.00', 'rating' => 4.6, 'reviews' => 22, 'image' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=800&q=70&auto=format&fit=crop'],
    ];
@endphp

<x-layouts.app title="Search" description="Search premium veteran-made gear — apparel, outdoor equipment, collectibles, flags, and more.">

    {{-- ============ Search hero ============ --}}
    <section class="border-b border-navy-100 bg-surface">
        <div class="mx-auto max-w-3xl px-4 py-16 text-center sm:px-6 lg:py-20">
            <p class="text-sm font-semibold tracking-widest text-bronze-600 uppercase">Search the armory</p>
            <h1 class="mt-3 font-display text-3xl font-bold text-navy-900 sm:text-4xl">What are you looking for?</h1>

            <form action="{{ route('search') }}" method="get" class="relative mt-8" data-search-form>
                <svg class="pointer-events-none absolute top-1/2 left-5 size-5.5 -translate-y-1/2 text-navy-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                </svg>
                <label for="page-search" class="sr-only">Search products</label>
                <input type="search" id="page-search" name="q" data-live-search autocomplete="off" autofocus
                       placeholder="Search jackets, flags, challenge coins…"
                       class="w-full rounded-2xl border border-navy-200 bg-canvas py-5 pr-28 pl-13 text-base text-ink shadow-soft transition-all duration-200 placeholder:text-navy-400 hover:border-navy-300 focus:border-transparent focus:bg-surface focus:shadow-card focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500 [&::-webkit-search-cancel-button]:hidden">
                <div class="absolute top-1/2 right-3 flex -translate-y-1/2 items-center gap-1.5">
                    <button type="button" data-search-clear hidden aria-label="Clear search"
                            class="flex size-9 items-center justify-center rounded-full text-navy-400 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-700">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                    </button>
                    <x-ui.button type="submit" size="sm">Search</x-ui.button>
                </div>
            </form>

            {{-- Suggested keywords --}}
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                <span class="text-sm text-navy-500">Try:</span>
                @foreach (['field jacket', 'rucksack', 'challenge coin', 'stitched flag', 'watch'] as $keyword)
                    <button type="button" data-search-suggestion="{{ $keyword }}"
                            class="rounded-full border border-navy-200 bg-surface px-3.5 py-1.5 text-sm font-medium text-navy-700 transition-all duration-200 hover:border-bronze-500 hover:bg-bronze-50 hover:text-bronze-700">
                        {{ $keyword }}
                    </button>
                @endforeach
            </div>

            {{-- Recent + popular searches --}}
            <div class="mt-10 grid grid-cols-1 gap-8 text-left sm:grid-cols-2">
                <div data-recent-searches-block hidden>
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-sm font-semibold tracking-wide text-navy-500 uppercase">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                            Recent searches
                        </h2>
                        <button type="button" data-recent-clear class="text-xs font-medium text-bronze-600 underline-offset-4 hover:underline">Clear</button>
                    </div>
                    <ul data-recent-searches class="mt-3 flex flex-wrap gap-2"></ul>
                </div>

                <div>
                    <h2 class="flex items-center gap-2 text-sm font-semibold tracking-wide text-navy-500 uppercase">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 17 10 11l4 4 6-7M20 8h-5m5 0v5"/></svg>
                        Popular right now
                    </h2>
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach (['ranger jacket', 'veteran gifts', 'american flag', 'edc kit', 'field watch', 'unit coins'] as $popular)
                            <li>
                                <button type="button" data-search-suggestion="{{ $popular }}"
                                        class="rounded-full bg-navy-50 px-3.5 py-1.5 text-sm font-medium text-navy-700 transition-all duration-200 hover:bg-navy-900 hover:text-white">
                                    {{ $popular }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ Results ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- Live filters --}}
        <div class="flex flex-wrap items-center gap-2" role="group" aria-label="Filter results by category">
            <button type="button" data-live-filter="all" aria-pressed="true"
                    class="rounded-full border border-navy-900 bg-navy-900 px-4 py-2 text-sm font-semibold text-white transition-all duration-200 aria-pressed:border-navy-900 aria-pressed:bg-navy-900 aria-pressed:text-white">
                All
            </button>
            @foreach ($liveFilterCategories as $filterCategory)
                <button type="button" data-live-filter="{{ $filterCategory }}" aria-pressed="false"
                        class="rounded-full border border-navy-200 bg-surface px-4 py-2 text-sm font-semibold text-navy-700 transition-all duration-200 hover:border-navy-400 aria-pressed:border-navy-900 aria-pressed:bg-navy-900 aria-pressed:text-white">
                    {{ $filterCategory }}
                </button>
            @endforeach
        </div>

        <p class="mt-6 text-sm text-navy-500" data-results-count aria-live="polite">
            Showing <span class="font-semibold text-navy-900">{{ count($results) }}</span> results
        </p>

        {{-- Result cards --}}
        <div data-search-results class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($results as $product)
                <x-ui.product-card
                    data-result-name="{{ Str::lower($product['name'].' '.$product['category']) }}"
                    data-result-category="{{ $product['category'] }}"
                    :name="$product['name']"
                    :category="$product['category']"
                    :price="$product['price']"
                    :old-price="$product['oldPrice']"
                    :badge="$product['badge']"
                    :badge-variant="$product['badgeVariant']"
                    :rating="$product['rating']"
                    :reviews="$product['reviews']"
                    :image="$product['image']"
                    :href="route('product.show')"
                />
            @endforeach
        </div>

        {{-- Empty state --}}
        <div data-search-empty hidden class="mx-auto max-w-md py-16 text-center">
            <svg class="mx-auto h-44 w-auto" viewBox="0 0 240 180" fill="none" aria-hidden="true">
                <circle cx="120" cy="86" r="64" class="fill-navy-100"/>
                <rect x="76" y="66" width="88" height="62" rx="10" class="fill-white stroke-navy-300" stroke-width="2.5"/>
                <path d="M76 82h88" class="stroke-navy-200" stroke-width="2.5"/>
                <circle cx="90" cy="74" r="2.6" class="fill-bronze-400"/>
                <circle cx="99" cy="74" r="2.6" class="fill-olive-400"/>
                <circle cx="108" cy="74" r="2.6" class="fill-navy-300"/>
                <path d="M96 104h48M96 114h30" class="stroke-navy-200" stroke-width="4" stroke-linecap="round"/>
                <circle cx="158" cy="118" r="26" class="fill-white stroke-navy-900" stroke-width="3.5"/>
                <path d="m177 137 16 16" class="stroke-navy-900" stroke-width="5" stroke-linecap="round"/>
                <path d="m150 110 16 16m0-16-16 16" class="stroke-bronze-500" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M56 44c3 0 5-2 5-5 0 3 2 5 5 5-3 0-5 2-5 5 0-3-2-5-5-5Z" class="fill-bronze-300"/>
                <path d="M178 34c4 0 7-3 7-7 0 4 3 7 7 7-4 0-7 3-7 7 0-4-3-7-7-7Z" class="fill-olive-300"/>
            </svg>
            <h2 class="mt-6 font-display text-xl font-bold text-navy-900">No matches found</h2>
            <p class="mt-2 text-navy-600">
                We couldn't find anything for &ldquo;<span data-empty-query class="font-semibold text-navy-900"></span>&rdquo;.
                Check the spelling, or try one of the popular searches above.
            </p>
            <x-ui.button type="button" data-search-reset variant="outline" class="mt-6">Clear search</x-ui.button>
        </div>
    </section>

    {{-- ============ Trending products ============ --}}
    <section class="bg-surface py-20 lg:py-24" data-reveal>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                <x-ui.section-heading
                    align="left"
                    eyebrow="Trending products"
                    title="Shoppers are searching for these"
                />
                <x-ui.button :href="route('shop')" variant="outline">Browse the full shop</x-ui.button>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($trendingProducts as $product)
                    <x-ui.product-card
                        :name="$product['name']"
                        :category="$product['category']"
                        :price="$product['price']"
                        :rating="$product['rating']"
                        :reviews="$product['reviews']"
                        :image="$product['image']"
                        :href="route('product.show')"
                    />
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
