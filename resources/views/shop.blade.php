@php
    $products = [
        ['name' => 'Ranger Field Jacket', 'category' => 'Apparel', 'price' => '$189.00', 'oldPrice' => '$249.00', 'badge' => '-24%', 'badgeVariant' => 'danger', 'rating' => 4.8, 'reviews' => 132, 'stock' => 'Only 14 left — order soon', 'stockPercent' => 18, 'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Patriot Canvas Rucksack', 'category' => 'Outdoor Gear', 'price' => '$149.00', 'oldPrice' => null, 'badge' => 'Best seller', 'badgeVariant' => 'bronze', 'rating' => 4.9, 'reviews' => 87, 'stock' => 'In stock', 'stockPercent' => null, 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Sentinel Field Watch', 'category' => 'Accessories', 'price' => '$229.00', 'oldPrice' => '$279.00', 'badge' => '-18%', 'badgeVariant' => 'danger', 'rating' => 4.7, 'reviews' => 64, 'stock' => 'In stock', 'stockPercent' => null, 'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Garrison Heritage Tee', 'category' => 'Apparel', 'price' => '$38.00', 'oldPrice' => null, 'badge' => 'New', 'badgeVariant' => 'olive', 'rating' => 4.6, 'reviews' => 53, 'stock' => 'In stock', 'stockPercent' => null, 'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Honor EDC Kit', 'category' => 'Everyday Carry', 'price' => '$96.00', 'oldPrice' => '$120.00', 'badge' => '-20%', 'badgeVariant' => 'danger', 'rating' => 4.6, 'reviews' => 41, 'stock' => 'Only 9 left — order soon', 'stockPercent' => 12, 'image' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Anniversary Stitched Flag', 'category' => 'Flags', 'price' => '$120.00', 'oldPrice' => null, 'badge' => 'Limited', 'badgeVariant' => 'navy', 'rating' => 5.0, 'reviews' => 28, 'stock' => 'Only 6 left — order soon', 'stockPercent' => 8, 'image' => 'https://images.unsplash.com/photo-1520095972714-909e91b038e5?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Heritage Automatic Watch', 'category' => 'Accessories', 'price' => '$449.00', 'oldPrice' => null, 'badge' => 'Limited', 'badgeVariant' => 'bronze', 'rating' => 4.9, 'reviews' => 19, 'stock' => 'In stock', 'stockPercent' => null, 'image' => 'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Field Manual Collection', 'category' => 'Books', 'price' => '$54.00', 'oldPrice' => '$68.00', 'badge' => '-21%', 'badgeVariant' => 'danger', 'rating' => 4.5, 'reviews' => 36, 'stock' => 'In stock', 'stockPercent' => null, 'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Trail Proven Boots', 'category' => 'Outdoor Gear', 'price' => '$210.00', 'oldPrice' => null, 'badge' => null, 'badgeVariant' => 'bronze', 'rating' => 4.7, 'reviews' => 72, 'stock' => 'Out of stock', 'stockPercent' => 0, 'image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&q=70&auto=format&fit=crop'],
    ];

    $categories = [
        ['name' => 'Apparel', 'count' => 48],
        ['name' => 'Military Collectibles', 'count' => 32],
        ['name' => 'Outdoor Gear', 'count' => 36],
        ['name' => 'Flags', 'count' => 21],
        ['name' => 'Challenge Coins', 'count' => 27],
        ['name' => 'Books', 'count' => 54],
        ['name' => 'Accessories', 'count' => 45],
        ['name' => 'Home Decor', 'count' => 30],
    ];

    $brands = [
        ['name' => 'Valor Supply Co.', 'count' => 112],
        ['name' => 'Garrison Works', 'count' => 47],
        ['name' => 'Sentinel & Sons', 'count' => 31],
        ['name' => 'Basecamp Provisions', 'count' => 24],
        ['name' => 'Old Glory Textiles', 'count' => 18],
    ];

    $filterColors = [
        ['name' => 'Olive Drab', 'class' => 'bg-olive-600'],
        ['name' => 'Coyote Brown', 'class' => 'bg-bronze-600'],
        ['name' => 'Midnight Navy', 'class' => 'bg-navy-900'],
        ['name' => 'Stone Gray', 'class' => 'bg-gray-400'],
        ['name' => 'Sand', 'class' => 'bg-bronze-200'],
        ['name' => 'Black', 'class' => 'bg-black'],
    ];

    $materials = [
        ['name' => 'Waxed Canvas', 'count' => 34],
        ['name' => 'Full-Grain Leather', 'count' => 28],
        ['name' => 'Organic Cotton', 'count' => 52],
        ['name' => 'Ripstop Nylon', 'count' => 23],
        ['name' => 'Merino Wool', 'count' => 17],
        ['name' => 'Stainless Steel', 'count' => 15],
    ];
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
            <h1 class="mt-4 font-display text-3xl font-bold text-navy-900 sm:text-4xl">All Products</h1>
            <p class="mt-2 max-w-xl text-navy-600">Field-grade gear, apparel, and collectibles — every piece backed by our lifetime craftsmanship warranty.</p>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex gap-10">

            {{-- ============ Filter sidebar ============ --}}
            <div data-filters-backdrop hidden class="fixed inset-0 z-40 bg-navy-950/50 backdrop-blur-sm lg:hidden"></div>

            <aside data-filters-panel
                   class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] -translate-x-full overflow-y-auto bg-surface p-6 shadow-card-hover transition-transform duration-300 ease-out lg:sticky lg:top-24 lg:z-auto lg:block lg:max-h-[calc(100vh-8rem)] lg:w-64 lg:shrink-0 lg:translate-x-0 lg:overflow-y-auto lg:bg-transparent lg:p-0 lg:shadow-none xl:w-72 scrollbar-none"
                   aria-label="Product filters">

                <div class="mb-2 flex items-center justify-between">
                    <h2 class="font-display text-lg font-bold text-navy-900">Filters</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" class="text-sm font-medium text-bronze-600 underline-offset-4 transition-colors duration-200 hover:text-bronze-700 hover:underline">
                            Clear all
                        </button>
                        <button type="button" data-filters-close aria-label="Close filters"
                                class="flex size-9 items-center justify-center rounded-xl text-navy-600 transition-colors duration-200 hover:bg-navy-50 lg:hidden">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Categories --}}
                <x-ui.accordion-item title="Categories" :open="true">
                    <ul class="space-y-2.5">
                        @foreach ($categories as $index => $category)
                            <li>
                                <label class="group flex cursor-pointer items-center justify-between gap-3">
                                    <span class="flex items-center gap-3">
                                        <input type="checkbox" @checked($index === 0)
                                               class="size-4.5 rounded-md border-navy-300 accent-olive-600">
                                        <span class="text-sm text-navy-700 transition-colors duration-200 group-hover:text-navy-900">{{ $category['name'] }}</span>
                                    </span>
                                    <span class="text-xs text-navy-400">{{ $category['count'] }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.accordion-item>

                {{-- Brand --}}
                <x-ui.accordion-item title="Brand" :open="true">
                    <ul class="space-y-2.5">
                        @foreach ($brands as $brand)
                            <li>
                                <label class="group flex cursor-pointer items-center justify-between gap-3">
                                    <span class="flex items-center gap-3">
                                        <input type="checkbox" class="size-4.5 rounded-md border-navy-300 accent-olive-600">
                                        <span class="text-sm text-navy-700 transition-colors duration-200 group-hover:text-navy-900">{{ $brand['name'] }}</span>
                                    </span>
                                    <span class="text-xs text-navy-400">{{ $brand['count'] }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.accordion-item>

                {{-- Price slider --}}
                <x-ui.accordion-item title="Price" :open="true">
                    <div data-price-range data-min="0" data-max="500">
                        <div class="relative h-5">
                            <div class="absolute top-1/2 right-0 left-0 h-1.5 -translate-y-1/2 rounded-full bg-navy-100"></div>
                            <div data-price-fill class="absolute top-1/2 h-1.5 -translate-y-1/2 rounded-full bg-olive-600" style="left: 10%; right: 20%"></div>
                            <input type="range" data-price-min min="0" max="500" step="5" value="50" class="price-range-input" aria-label="Minimum price">
                            <input type="range" data-price-max min="0" max="500" step="5" value="400" class="price-range-input" aria-label="Maximum price">
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-3">
                            <span class="rounded-lg border border-navy-200 bg-surface px-3 py-1.5 text-sm font-semibold text-navy-900 tabular-nums" data-price-min-label>$50</span>
                            <span class="h-px w-4 bg-navy-300" aria-hidden="true"></span>
                            <span class="rounded-lg border border-navy-200 bg-surface px-3 py-1.5 text-sm font-semibold text-navy-900 tabular-nums" data-price-max-label>$400</span>
                        </div>
                    </div>
                </x-ui.accordion-item>

                {{-- Ratings --}}
                <x-ui.accordion-item title="Ratings" :open="true">
                    <ul class="space-y-2.5">
                        @foreach ([5, 4, 3, 2] as $stars)
                            <li>
                                <label class="group flex cursor-pointer items-center gap-3">
                                    <input type="radio" name="rating-filter" class="size-4.5 accent-olive-600">
                                    <x-ui.rating :value="$stars" size="sm" />
                                    <span class="text-sm text-navy-600 transition-colors duration-200 group-hover:text-navy-900">
                                        {{ $stars === 5 ? '5 stars' : $stars.' stars & up' }}
                                    </span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.accordion-item>

                {{-- Availability --}}
                <x-ui.accordion-item title="Availability">
                    <ul class="space-y-2.5">
                        @foreach ([['In stock', 218], ['Pre-order', 12], ['Out of stock', 9]] as [$label, $count])
                            <li>
                                <label class="group flex cursor-pointer items-center justify-between gap-3">
                                    <span class="flex items-center gap-3">
                                        <input type="checkbox" @checked($loop->first) class="size-4.5 rounded-md border-navy-300 accent-olive-600">
                                        <span class="text-sm text-navy-700 transition-colors duration-200 group-hover:text-navy-900">{{ $label }}</span>
                                    </span>
                                    <span class="text-xs text-navy-400">{{ $count }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.accordion-item>

                {{-- Color --}}
                <x-ui.accordion-item title="Color">
                    <div class="flex flex-wrap gap-3">
                        @foreach ($filterColors as $color)
                            <button type="button" data-filter-chip aria-pressed="false" aria-label="Filter by color: {{ $color['name'] }}" title="{{ $color['name'] }}"
                                    class="flex size-9 items-center justify-center rounded-full ring-2 ring-transparent ring-offset-2 transition-all duration-200 hover:ring-navy-300 aria-pressed:ring-navy-900">
                                <span class="size-7 rounded-full {{ $color['class'] }} shadow-inner"></span>
                            </button>
                        @endforeach
                    </div>
                </x-ui.accordion-item>

                {{-- Size --}}
                <x-ui.accordion-item title="Size">
                    <div class="flex flex-wrap gap-2">
                        @foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                            <button type="button" data-filter-chip aria-pressed="false"
                                    class="min-w-12 rounded-xl border border-navy-200 bg-surface px-3 py-2 text-sm font-semibold text-navy-700 transition-all duration-200 hover:border-navy-400 aria-pressed:border-navy-900 aria-pressed:bg-navy-900 aria-pressed:text-white">
                                {{ $size }}
                            </button>
                        @endforeach
                    </div>
                </x-ui.accordion-item>

                {{-- Material --}}
                <x-ui.accordion-item title="Material" class="border-b-0">
                    <ul class="space-y-2.5">
                        @foreach ($materials as $material)
                            <li>
                                <label class="group flex cursor-pointer items-center justify-between gap-3">
                                    <span class="flex items-center gap-3">
                                        <input type="checkbox" class="size-4.5 rounded-md border-navy-300 accent-olive-600">
                                        <span class="text-sm text-navy-700 transition-colors duration-200 group-hover:text-navy-900">{{ $material['name'] }}</span>
                                    </span>
                                    <span class="text-xs text-navy-400">{{ $material['count'] }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.accordion-item>

                <x-ui.button variant="secondary" class="mt-6 w-full lg:hidden" data-filters-close>
                    Show 227 results
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
                        <input type="search" placeholder="Search in shop…"
                               class="w-full rounded-xl border border-navy-200 bg-surface py-2.5 pr-4 pl-10 text-sm text-ink placeholder:text-navy-400 transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                    </label>

                    <label class="flex items-center gap-2">
                        <span class="hidden text-sm text-navy-500 sm:block">Sort by</span>
                        <select class="rounded-xl border border-navy-200 bg-surface px-3.5 py-2.5 text-sm font-medium text-navy-800 transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                            <option>Featured</option>
                            <option>Best selling</option>
                            <option>Price: low to high</option>
                            <option>Price: high to low</option>
                            <option>Highest rated</option>
                            <option>Newest</option>
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

                <p class="mt-5 text-sm text-navy-500">Showing <span class="font-semibold text-navy-900">1–9</span> of <span class="font-semibold text-navy-900">227</span> products</p>

                {{-- Product grid --}}
                <div data-product-view="grid"
                     class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($products as $product)
                        <x-ui.product-card
                            :name="$product['name']"
                            :category="$product['category']"
                            :price="$product['price']"
                            :old-price="$product['oldPrice']"
                            :badge="$product['badge']"
                            :badge-variant="$product['badgeVariant']"
                            :rating="$product['rating']"
                            :reviews="$product['reviews']"
                            :stock="$product['stock']"
                            :stock-percent="$product['stockPercent']"
                            :image="$product['image']"
                            :href="route('product.show')"
                        />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <nav class="mt-14 flex items-center justify-center gap-1.5" aria-label="Pagination">
                    <a href="#" aria-disabled="true"
                       class="flex size-11 items-center justify-center rounded-xl border border-navy-200 text-navy-300">
                        <span class="sr-only">Previous page</span>
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
                    </a>
                    <a href="#" aria-current="page"
                       class="flex size-11 items-center justify-center rounded-xl bg-navy-900 text-sm font-semibold text-white shadow-soft">1</a>
                    @foreach ([2, 3] as $page)
                        <a href="#" class="flex size-11 items-center justify-center rounded-xl border border-navy-200 text-sm font-semibold text-navy-700 transition-all duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white">{{ $page }}</a>
                    @endforeach
                    <span class="flex size-11 items-end justify-center pb-2 text-navy-400" aria-hidden="true">…</span>
                    <a href="#" class="flex size-11 items-center justify-center rounded-xl border border-navy-200 text-sm font-semibold text-navy-700 transition-all duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white">26</a>
                    <a href="#"
                       class="flex size-11 items-center justify-center rounded-xl border border-navy-200 text-navy-700 transition-all duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white">
                        <span class="sr-only">Next page</span>
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</x-layouts.app>
