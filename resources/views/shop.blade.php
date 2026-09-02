@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Product> $products */
    /** @var \App\DTOs\Shop\ShopFilters $filters */
    $priceMin = $filters->minPriceCents !== null ? $filters->minPriceCents / 100 : $priceRange['min_dollars'];
    $priceMax = $filters->maxPriceCents !== null ? $filters->maxPriceCents / 100 : $priceRange['max_dollars'];
    $sliderMax = max(500, (int) (ceil($priceRange['max_dollars'] / 5) * 5));
    $sliderMin = (int) floor($priceRange['min_dollars']);
@endphp

<x-layouts.app title="Shop All Products" description="Browse premium veteran-made apparel, outdoor gear, collectibles, and everyday carry. Filter by category, brand, price, and more.">

    {{-- Page header --}}
    <div class="border-b border-navy-100 bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                    <li><a href="{{ route('home') }}" class="transition-colors duration-200 hover:text-navy-900">Home</a></li>
                    <li aria-hidden="true">/</li>
                    <li aria-current="page" class="font-medium text-navy-900">Shop</li>
                </ol>
            </nav>
            <h1 class="mt-4 font-display text-3xl font-bold text-navy-900 sm:text-4xl">
                {{ $currentCategory ?? 'All Products' }}
            </h1>
            <p class="mt-2 max-w-xl text-navy-600">Field-grade gear, apparel, and collectibles — every piece backed by our lifetime craftsmanship warranty.</p>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8" data-shop>
        <form method="GET" action="{{ route('shop') }}" data-shop-form>
            <div class="flex gap-10">

                {{-- ============ Filter sidebar ============ --}}
                <div data-filters-backdrop hidden class="fixed inset-0 z-40 bg-navy-950/50 backdrop-blur-sm lg:hidden"></div>

                <aside data-filters-panel
                       class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] -translate-x-full overflow-y-auto bg-surface p-6 shadow-card-hover transition-transform duration-300 ease-out lg:sticky lg:top-24 lg:z-auto lg:block lg:max-h-[calc(100vh-8rem)] lg:w-64 lg:shrink-0 lg:translate-x-0 lg:overflow-y-auto lg:bg-transparent lg:p-0 lg:shadow-none xl:w-72 scrollbar-none"
                       aria-label="Product filters">

                    <div class="mb-2 flex items-center justify-between">
                        <h2 class="font-display text-lg font-bold text-navy-900">Filters</h2>
                        <div class="flex items-center gap-2">
                            @if ($filters->hasActiveFilters())
                                <a href="{{ route('shop') }}" data-clear-filters
                                   class="text-sm font-medium text-bronze-600 underline-offset-4 transition-colors duration-200 hover:text-bronze-700 hover:underline">
                                    Clear all
                                </a>
                            @endif
                            <button type="button" data-filters-close aria-label="Close filters"
                                    class="flex size-9 items-center justify-center rounded-xl text-navy-600 transition-colors duration-200 hover:bg-navy-50 lg:hidden">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Categories --}}
                    <x-ui.accordion-item title="Categories" :open="true">
                        <ul class="space-y-2.5">
                            @forelse ($categories as $category)
                                <li>
                                    <label class="group flex cursor-pointer items-center justify-between gap-3">
                                        <span class="flex items-center gap-3">
                                            <input type="checkbox" name="category[]" value="{{ $category['slug'] }}"
                                                   @checked(in_array($category['slug'], $filters->categories, true))
                                                   class="size-4.5 rounded-md border-navy-300 accent-olive-600">
                                            <span class="text-sm text-navy-700 transition-colors duration-200 group-hover:text-navy-900">{{ $category['name'] }}</span>
                                        </span>
                                        <span class="text-xs text-navy-400">{{ $category['count'] }}</span>
                                    </label>
                                </li>
                            @empty
                                <li class="text-sm text-navy-500">No categories available.</li>
                            @endforelse
                        </ul>
                    </x-ui.accordion-item>

                    {{-- Brand --}}
                    <x-ui.accordion-item title="Brand" :open="true">
                        <ul class="space-y-2.5">
                            @forelse ($brands as $brand)
                                <li>
                                    <label class="group flex cursor-pointer items-center justify-between gap-3">
                                        <span class="flex items-center gap-3">
                                            <input type="checkbox" name="brand[]" value="{{ $brand['slug'] }}"
                                                   @checked(in_array($brand['slug'], $filters->brands, true))
                                                   class="size-4.5 rounded-md border-navy-300 accent-olive-600">
                                            <span class="text-sm text-navy-700 transition-colors duration-200 group-hover:text-navy-900">{{ $brand['name'] }}</span>
                                        </span>
                                        <span class="text-xs text-navy-400">{{ $brand['count'] }}</span>
                                    </label>
                                </li>
                            @empty
                                <li class="text-sm text-navy-500">No brands available.</li>
                            @endforelse
                        </ul>
                    </x-ui.accordion-item>

                    {{-- Price slider --}}
                    <x-ui.accordion-item title="Price" :open="true">
                        <div data-price-range data-min="{{ $sliderMin }}" data-max="{{ $sliderMax }}">
                            <div class="relative h-5">
                                <div class="absolute top-1/2 right-0 left-0 h-1.5 -translate-y-1/2 rounded-full bg-navy-100"></div>
                                <div data-price-fill class="absolute top-1/2 h-1.5 -translate-y-1/2 rounded-full bg-olive-600"></div>
                                <input type="range" data-price-min min="{{ $sliderMin }}" max="{{ $sliderMax }}" step="5"
                                       value="{{ (int) $priceMin }}" class="price-range-input" aria-label="Minimum price">
                                <input type="range" data-price-max min="{{ $sliderMin }}" max="{{ $sliderMax }}" step="5"
                                       value="{{ (int) $priceMax }}" class="price-range-input" aria-label="Maximum price">
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <span class="rounded-lg border border-navy-200 bg-surface px-3 py-1.5 text-sm font-semibold text-navy-900 tabular-nums" data-price-min-label>${{ number_format($priceMin, 0) }}</span>
                                <span class="h-px w-4 bg-navy-300" aria-hidden="true"></span>
                                <span class="rounded-lg border border-navy-200 bg-surface px-3 py-1.5 text-sm font-semibold text-navy-900 tabular-nums" data-price-max-label>${{ number_format($priceMax, 0) }}</span>
                            </div>
                            <input type="hidden" name="min_price" data-price-min-input value="{{ $filters->minPriceCents !== null ? $priceMin : '' }}">
                            <input type="hidden" name="max_price" data-price-max-input value="{{ $filters->maxPriceCents !== null ? $priceMax : '' }}">
                        </div>
                    </x-ui.accordion-item>

                    {{-- Availability --}}
                    <x-ui.accordion-item title="Availability">
                        <ul class="space-y-2.5">
                            <li>
                                <label class="group flex cursor-pointer items-center gap-3">
                                    <input type="hidden" name="in_stock" value="0">
                                    <input type="checkbox" name="in_stock" value="1" @checked($filters->inStock)
                                           class="size-4.5 rounded-md border-navy-300 accent-olive-600">
                                    <span class="text-sm text-navy-700 transition-colors duration-200 group-hover:text-navy-900">In stock</span>
                                </label>
                            </li>
                        </ul>
                    </x-ui.accordion-item>

                    {{-- Featured / Sale / New --}}
                    <x-ui.accordion-item title="Highlights">
                        <ul class="space-y-2.5">
                            <li>
                                <label class="group flex cursor-pointer items-center gap-3">
                                    <input type="checkbox" name="featured" value="1" @checked($filters->featured)
                                           class="size-4.5 rounded-md border-navy-300 accent-olive-600">
                                    <span class="text-sm text-navy-700 transition-colors duration-200 group-hover:text-navy-900">Featured products</span>
                                </label>
                            </li>
                            <li>
                                <label class="group flex cursor-pointer items-center gap-3">
                                    <input type="checkbox" name="on_sale" value="1" @checked($filters->onSale)
                                           class="size-4.5 rounded-md border-navy-300 accent-olive-600">
                                    <span class="text-sm text-navy-700 transition-colors duration-200 group-hover:text-navy-900">On sale</span>
                                </label>
                            </li>
                            <li>
                                <label class="group flex cursor-pointer items-center gap-3">
                                    <input type="checkbox" name="new_arrival" value="1" @checked($filters->newArrival)
                                           class="size-4.5 rounded-md border-navy-300 accent-olive-600">
                                    <span class="text-sm text-navy-700 transition-colors duration-200 group-hover:text-navy-900">New arrivals</span>
                                </label>
                            </li>
                        </ul>
                    </x-ui.accordion-item>

                    {{-- Future-ready filters (UI preserved) --}}
                    <x-ui.accordion-item title="Ratings" :open="false">
                        <ul class="space-y-2.5">
                            @foreach ([5, 4, 3, 2] as $stars)
                                <li>
                                    <label class="group flex cursor-pointer items-center gap-3 opacity-60">
                                        <input type="radio" name="rating-filter" disabled class="size-4.5 accent-olive-600">
                                        <x-ui.rating :value="$stars" size="sm" />
                                        <span class="text-sm text-navy-600">{{ $stars === 5 ? '5 stars' : $stars.' stars & up' }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </x-ui.accordion-item>

                    <x-ui.button type="submit" variant="secondary" class="mt-6 w-full lg:hidden" data-filters-close>
                        Show {{ $products->total() }} results
                    </x-ui.button>
                </aside>

                {{-- ============ Main content ============ --}}
                <div class="min-w-0 flex-1">

                    {{-- Toolbar --}}
                    <div class="flex flex-wrap items-center gap-3 rounded-card bg-surface p-4 shadow-soft">
                        <button type="button" data-filters-toggle
                                class="flex items-center gap-2 rounded-xl border border-navy-200 px-4 py-2.5 text-sm font-semibold text-navy-800 transition-colors duration-200 hover:border-navy-400 lg:hidden">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                                <path d="M4 6h16M7 12h10M10 18h4"/>
                            </svg>
                            Filters
                        </button>

                        <label class="relative min-w-40 flex-1">
                            <span class="sr-only">Search products</span>
                            <svg class="pointer-events-none absolute top-1/2 left-3.5 size-4.5 -translate-y-1/2 text-navy-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                            </svg>
                            <input type="search" name="search" value="{{ $filters->search }}" placeholder="Search in shop…" data-shop-search
                                   class="w-full rounded-xl border border-navy-200 bg-surface py-2.5 pr-4 pl-10 text-sm text-ink placeholder:text-navy-400 transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                        </label>

                        <label class="flex items-center gap-2">
                            <span class="hidden text-sm text-navy-500 sm:block">Sort by</span>
                            <select name="sort" data-shop-sort
                                    class="rounded-xl border border-navy-200 bg-surface px-3.5 py-2.5 text-sm font-medium text-navy-800 transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                @foreach ($sortOptions as $option)
                                    <option value="{{ $option->value }}" @selected($filters->sort === $option)>{{ $option->label() }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="hidden items-center gap-2 sm:flex">
                            <span class="text-sm text-navy-500">Show</span>
                            <select name="per_page" data-shop-per-page
                                    class="rounded-xl border border-navy-200 bg-surface px-3.5 py-2.5 text-sm font-medium text-navy-800 transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                @foreach ($perPageOptions as $option)
                                    <option value="{{ $option }}" @selected($filters->perPage === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </label>

                        <div class="flex rounded-xl border border-navy-200 p-1" role="group" aria-label="View layout">
                            <button type="button" data-view-grid aria-pressed="true" aria-label="Grid view"
                                    class="flex size-9 items-center justify-center rounded-lg bg-navy-900 text-white transition-colors duration-200">
                                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                                    <rect x="4" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.5"/>
                                </svg>
                            </button>
                            <button type="button" data-view-list aria-pressed="false" aria-label="List view"
                                    class="flex size-9 items-center justify-center rounded-lg text-navy-500 transition-colors duration-200 hover:text-navy-900">
                                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                                    <path d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Results summary --}}
                    <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-navy-500">
                        <p>
                            Showing
                            <span class="font-semibold text-navy-900">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span>
                            of
                            <span class="font-semibold text-navy-900">{{ $products->total() }}</span>
                            products
                        </p>

                        @if ($activeFilterLabels !== [])
                            <span class="hidden h-4 w-px bg-navy-200 sm:block" aria-hidden="true"></span>
                            <ul class="flex flex-wrap gap-2">
                                @foreach ($activeFilterLabels as $label)
                                    <li><x-ui.badge variant="neutral">{{ $label }}</x-ui.badge></li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($filters->hasActiveFilters())
                            <a href="{{ route('shop') }}" class="font-medium text-bronze-600 underline-offset-4 hover:underline">Clear filters</a>
                        @endif
                    </div>

                    {{-- Skeleton loaders --}}
                    <div data-shop-skeleton hidden class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                        @for ($i = 0; $i < 8; $i++)
                            <div class="animate-pulse overflow-hidden rounded-card bg-surface shadow-card">
                                <div class="aspect-4/5 bg-navy-100"></div>
                                <div class="space-y-3 p-5">
                                    <div class="h-3 w-1/3 rounded bg-navy-100"></div>
                                    <div class="h-4 w-2/3 rounded bg-navy-100"></div>
                                    <div class="h-4 w-1/2 rounded bg-navy-100"></div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    {{-- Product results --}}
                    <div data-shop-results @class(['mt-6', 'hidden' => false])>
                        @if ($products->isEmpty())
                            <div class="py-20 text-center" data-shop-empty>
                                <svg class="mx-auto h-36 w-auto" viewBox="0 0 200 150" fill="none" aria-hidden="true">
                                    <circle cx="100" cy="72" r="54" class="fill-navy-100"/>
                                    <path d="M62 52h14l10 46a6 6 0 0 0 6 5h34a6 6 0 0 0 6-5l8-34H82" class="stroke-navy-900" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="white"/>
                                    <circle cx="96" cy="118" r="6" class="fill-navy-900"/>
                                    <circle cx="122" cy="118" r="6" class="fill-navy-900"/>
                                    <path d="M104 76l12 12m0-12-12 12" class="stroke-bronze-500" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                                <h2 class="mt-6 font-display text-xl font-bold text-navy-900">No products found</h2>
                                <p class="mt-2 text-navy-600">Try adjusting your filters or search terms.</p>
                                <x-ui.button :href="route('shop')" variant="secondary" class="mt-6">Reset filters</x-ui.button>
                            </div>
                        @else
                            <div data-product-grid data-product-view="grid"
                                 class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                                @foreach ($products as $product)
                                    @php
                                        $badge = $product->shopBadge();
                                    @endphp
                                    <x-ui.product-card
                                        :name="$product->name"
                                        :brand="$product->brand?->name"
                                        :category="$product->category?->name"
                                        :short-description="$product->short_description"
                                        :price="$product->formattedPrice()"
                                        :old-price="$product->isOnSale() ? $product->formattedCompareAtPrice() : null"
                                        :badge="$badge['badge']"
                                        :badge-variant="$badge['variant']"
                                        :rating="$product->displayRating()"
                                        :reviews="$product->displayReviewCount()"
                                        :stock="$product->shopStockLabel()"
                                        :stock-percent="$product->shopStockPercent()"
                                        :image="$product->primaryImageUrl()"
                                        :href="route('product.show', $product)"
                                        :product-id="$product->id"
                                    />
                                @endforeach
                            </div>

                            <div data-product-list hidden class="mt-6 flex flex-col gap-6">
                                @foreach ($products as $product)
                                    @php
                                        $badge = $product->shopBadge();
                                    @endphp
                                    <x-ui.product-list-item
                                        :name="$product->name"
                                        :brand="$product->brand?->name"
                                        :category="$product->category?->name"
                                        :short-description="$product->short_description"
                                        :price="$product->formattedPrice()"
                                        :old-price="$product->isOnSale() ? $product->formattedCompareAtPrice() : null"
                                        :badge="$badge['badge']"
                                        :badge-variant="$badge['variant']"
                                        :rating="$product->displayRating()"
                                        :reviews="$product->displayReviewCount()"
                                        :stock="$product->shopStockLabel()"
                                        :stock-percent="$product->shopStockPercent()"
                                        :image="$product->primaryImageUrl()"
                                        :href="route('product.show', $product)"
                                        :product-id="$product->id"
                                    />
                                @endforeach
                            </div>

                            <x-ui.shop-pagination :paginator="$products" />
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
