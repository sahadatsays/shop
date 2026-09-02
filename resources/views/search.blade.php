<x-layouts.app title="Search" description="Search premium veteran-made gear — apparel, outdoor equipment, collectibles, flags, and more.">

    {{-- ============ Search hero ============ --}}
    <section class="border-b border-navy-100 bg-surface">
        <div class="mx-auto max-w-3xl px-4 py-16 text-center sm:px-6 lg:py-20">
            <p class="text-sm font-semibold tracking-widest text-bronze-600 uppercase">Search the armory</p>
            <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">What are you looking for?</h1>

            <form action="{{ route('search') }}" method="get" class="relative mt-8" data-search-form>
                <svg class="pointer-events-none absolute top-1/2 left-5 size-5.5 -translate-y-1/2 text-navy-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                </svg>
                <label for="page-search" class="sr-only">Search products</label>
                <input type="search" id="page-search" name="q" data-live-search autocomplete="off" autofocus
                       value="{{ $query }}"
                       placeholder="Search jackets, flags, challenge coins…"
                       class="w-full rounded-2xl border border-navy-200 bg-canvas py-5 pr-28 pl-13 text-base text-ink shadow-soft transition-all duration-200 placeholder:text-navy-400 hover:border-navy-300 focus:border-transparent focus:bg-surface focus:shadow-card focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500 [&::-webkit-search-cancel-button]:hidden">
                <div class="absolute top-1/2 right-3 flex -translate-y-1/2 items-center gap-1.5">
                    <button type="button" data-search-clear @hidden($query === '') aria-label="Clear search"
                            class="flex size-9 items-center justify-center rounded-full text-navy-400 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-700">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                    </button>
                    <x-ui.button type="submit" size="sm">Search</x-ui.button>
                </div>
            </form>

            <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                <span class="text-sm text-navy-500">Try:</span>
                @foreach ($popularSearches as $keyword)
                    <a href="{{ route('search', ['q' => $keyword]) }}" data-search-suggestion="{{ $keyword }}"
                            class="rounded-full border border-navy-200 bg-surface px-3.5 py-1.5 text-sm font-medium text-navy-700 transition-all duration-200 hover:border-bronze-500 hover:bg-bronze-50 hover:text-bronze-700">
                        {{ $keyword }}
                    </a>
                @endforeach
            </div>

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
                        @foreach ($popularSearches as $popular)
                            <li>
                                <a href="{{ route('search', ['q' => $popular]) }}" data-search-suggestion="{{ $popular }}"
                                        class="rounded-full bg-navy-50 px-3.5 py-1.5 text-sm font-medium text-navy-700 transition-all duration-200 hover:bg-navy-900 hover:text-white">
                                    {{ $popular }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        @if ($filterCategories->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2" role="group" aria-label="Filter results by category">
                <button type="button" data-live-filter="all" aria-pressed="true"
                        class="rounded-full border border-navy-900 bg-navy-900 px-4 py-2 text-sm font-semibold text-white transition-all duration-200">
                    All
                </button>
                @foreach ($filterCategories as $filterCategory)
                    <button type="button" data-live-filter="{{ $filterCategory }}" aria-pressed="false"
                            class="rounded-full border border-navy-200 bg-surface px-4 py-2 text-sm font-semibold text-navy-700 transition-all duration-200 hover:border-navy-400 aria-pressed:border-navy-900 aria-pressed:bg-navy-900 aria-pressed:text-white">
                        {{ $filterCategory }}
                    </button>
                @endforeach
            </div>
        @endif

        <p class="mt-6 text-sm text-navy-500" data-results-count aria-live="polite">
            Showing <span class="font-semibold text-navy-900">{{ $results->total() }}</span> {{ $results->total() === 1 ? 'result' : 'results' }}
            @if ($query !== '')
                for &ldquo;<span class="font-semibold text-navy-900">{{ $query }}</span>&rdquo;
            @endif
        </p>

        <div data-search-results class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4" @hidden($results->isEmpty())>
            @foreach ($results as $product)
                @php $badge = $product->shopBadge(); @endphp
                <x-ui.product-card
                    data-result-name="{{ Str::lower($product->name.' '.$product->sku.' '.($product->category?->name ?? '').' '.($product->brand?->name ?? '')) }}"
                    data-result-category="{{ $product->category?->name }}"
                    :name="$product->name"
                    :category="$product->category?->name"
                    :price="$product->formattedPrice()"
                    :old-price="$product->isOnSale() ? $product->formattedCompareAtPrice() : null"
                    :badge="$badge['badge']"
                    :badge-variant="$badge['variant']"
                    :rating="$product->displayRating()"
                    :reviews="$product->displayReviewCount()"
                    :image="$product->primaryImageUrl()"
                    :href="route('product.show', $product)"
                    :product-id="$product->id"
                />
            @endforeach
        </div>

        <div data-search-empty @class(['mx-auto max-w-md py-16 text-center', 'hidden' => $results->isNotEmpty()])>
            <h2 class="mt-6 font-display text-xl font-bold text-navy-900">No matches found</h2>
            <p class="mt-2 text-navy-600">
                We couldn't find anything for &ldquo;<span data-empty-query class="font-semibold text-navy-900">{{ $query }}</span>&rdquo;.
                Check the spelling, or try one of the popular searches above.
            </p>
            <x-ui.button :href="route('search')" variant="outline" class="mt-6">Clear search</x-ui.button>
        </div>

        @if ($results->hasPages())
            <div class="mt-10">
                {{ $results->withQueryString()->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>
