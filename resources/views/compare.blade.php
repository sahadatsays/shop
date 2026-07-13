@php
    $products = [
        [
            'name' => 'Ranger Field Jacket',
            'price' => '$189.00',
            'oldPrice' => '$249.00',
            'rating' => 4.9,
            'reviews' => 132,
            'availability' => 'In stock',
            'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&q=70&auto=format&fit=crop',
            'specs' => ['weight' => '1.4 lb', 'fit' => 'Regular', 'sizes' => 'S – 3XL', 'origin' => 'Made in USA', 'care' => 'Spot clean, re-wax yearly'],
            'materials' => '10 oz waxed canvas shell, quilted flannel lining, antique brass hardware',
            'warranty' => 'Lifetime craftsmanship warranty',
            'features' => ['water' => true, 'ykk' => true, 'repair' => true, 'recycled' => false, 'pockets' => true],
        ],
        [
            'name' => 'Expedition Shell Jacket',
            'price' => '$219.00',
            'oldPrice' => null,
            'rating' => 4.7,
            'reviews' => 58,
            'availability' => 'In stock',
            'image' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&q=70&auto=format&fit=crop',
            'specs' => ['weight' => '0.9 lb', 'fit' => 'Athletic', 'sizes' => 'XS – XXL', 'origin' => 'Made in Vietnam', 'care' => 'Machine wash cold'],
            'materials' => '3-layer ripstop nylon, taped seams, laser-cut mesh venting',
            'warranty' => '5-year warranty',
            'features' => ['water' => true, 'ykk' => true, 'repair' => false, 'recycled' => true, 'pockets' => true],
        ],
        [
            'name' => 'Garrison Wool Overcoat',
            'price' => '$259.00',
            'oldPrice' => null,
            'rating' => 4.8,
            'reviews' => 74,
            'availability' => 'Low stock',
            'image' => 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=800&q=70&auto=format&fit=crop',
            'specs' => ['weight' => '2.1 lb', 'fit' => 'Tailored', 'sizes' => 'S – XXL', 'origin' => 'Made in USA', 'care' => 'Dry clean only'],
            'materials' => 'Melton wool blend shell, satin viscose lining, horn buttons',
            'warranty' => 'Lifetime craftsmanship warranty',
            'features' => ['water' => false, 'ykk' => true, 'repair' => true, 'recycled' => false, 'pockets' => true],
        ],
    ];

    $featureLabels = [
        'water' => 'Water resistant',
        'ykk' => 'YKK zippers & hardware',
        'repair' => 'Lifetime repair program',
        'recycled' => 'Recycled materials',
        'pockets' => 'Interior media pockets',
    ];

    $specLabels = [
        'weight' => 'Weight',
        'fit' => 'Fit',
        'sizes' => 'Available sizes',
        'origin' => 'Origin',
        'care' => 'Care',
    ];
@endphp

<x-layouts.app title="Compare Products" description="Compare Valor Supply Co. gear side by side — prices, specifications, materials, warranty, and features at a glance.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-compare>

        {{-- Header --}}
        <nav aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                <li><a href="{{ route('home') }}" class="transition-colors duration-200 hover:text-navy-900">Home</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="font-medium text-navy-900">Compare</li>
            </ol>
        </nav>

        <div class="mt-4 flex flex-wrap items-end justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-baseline gap-3">
                    <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">Compare products</h1>
                    <p class="text-navy-500" data-compare-count>3 products</p>
                </div>
                <p class="mt-2 max-w-xl text-navy-600">Side by side, spec by spec — find the piece that earns its place in your kit.</p>
            </div>

            <label class="flex cursor-pointer items-center gap-3 rounded-xl bg-surface px-4 py-2.5 shadow-soft">
                <input type="checkbox" data-compare-diff-toggle
                       class="size-4.5 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                <span class="text-sm font-medium text-navy-800">Highlight differences</span>
            </label>
        </div>

        {{-- Comparison table --}}
        <div class="mt-8 overflow-x-auto rounded-card bg-surface shadow-card lg:overflow-x-visible" data-compare-scroller>
            <table class="w-full min-w-190 border-collapse text-sm" data-compare-table>
                <caption class="sr-only">Side-by-side comparison of {{ count($products) }} products</caption>

                {{-- Sticky compact header --}}
                <thead>
                    <tr>
                        <th scope="col" class="sticky top-16 left-0 z-30 w-44 border-b border-navy-100 bg-surface/95 p-5 text-left align-bottom backdrop-blur-sm lg:top-18 lg:w-52">
                            <span class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Comparing</span>
                        </th>
                        @foreach ($products as $index => $product)
                            <th scope="col" data-col="{{ $index }}"
                                class="sticky top-16 z-20 border-b border-navy-100 bg-surface/95 p-5 text-left align-bottom backdrop-blur-sm lg:top-18">
                                <div class="flex items-start gap-3">
                                    <img src="{{ $product['image'] }}" alt="" loading="lazy" class="hidden size-14 shrink-0 rounded-xl object-cover sm:block">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-display text-sm font-bold text-navy-900">{{ $product['name'] }}</p>
                                        <p class="mt-0.5 font-display text-base font-extrabold text-navy-900">{{ $product['price'] }}</p>
                                        <div class="mt-2 flex items-center gap-2">
                                            <x-ui.button size="sm" class="px-3! py-1.5! text-xs!">Add to cart</x-ui.button>
                                            <button type="button" data-compare-remove aria-label="Remove {{ $product['name'] }} from comparison"
                                                    class="flex size-7 items-center justify-center rounded-lg text-navy-400 transition-colors duration-200 hover:bg-red-50 hover:text-red-600">
                                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    {{-- Images --}}
                    <tr data-compare-row>
                        <th scope="row" class="sticky left-0 z-10 bg-surface p-5 text-left align-top font-semibold text-navy-900">Image</th>
                        @foreach ($products as $index => $product)
                            <td data-col="{{ $index }}" class="p-5 align-top">
                                <a href="{{ route('product.show') }}" class="group block overflow-hidden rounded-card">
                                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy"
                                         class="aspect-4/3 w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                </a>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Price --}}
                    <tr data-compare-row class="border-t border-navy-100">
                        <th scope="row" class="sticky left-0 z-10 bg-surface p-5 text-left align-top font-semibold text-navy-900">Price</th>
                        @foreach ($products as $index => $product)
                            <td data-col="{{ $index }}" data-compare-value class="p-5 align-top">
                                <span class="font-display text-lg font-extrabold text-navy-900">{{ $product['price'] }}</span>
                                @if ($product['oldPrice'])
                                    <span class="ml-2 text-navy-400 line-through">{{ $product['oldPrice'] }}</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    {{-- Rating --}}
                    <tr data-compare-row class="border-t border-navy-100">
                        <th scope="row" class="sticky left-0 z-10 bg-surface p-5 text-left align-top font-semibold text-navy-900">Rating</th>
                        @foreach ($products as $index => $product)
                            <td data-col="{{ $index }}" data-compare-value class="p-5 align-top">
                                <span class="flex items-center gap-2">
                                    <x-ui.rating :value="$product['rating']" size="sm" />
                                    <span class="text-navy-600">{{ $product['rating'] }} ({{ $product['reviews'] }})</span>
                                </span>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Availability --}}
                    <tr data-compare-row class="border-t border-navy-100">
                        <th scope="row" class="sticky left-0 z-10 bg-surface p-5 text-left align-top font-semibold text-navy-900">Availability</th>
                        @foreach ($products as $index => $product)
                            <td data-col="{{ $index }}" data-compare-value class="p-5 align-top">
                                <span class="inline-flex items-center gap-2 font-medium {{ $product['availability'] === 'Low stock' ? 'text-bronze-700' : 'text-green-700' }}">
                                    <span class="size-2 rounded-full {{ $product['availability'] === 'Low stock' ? 'bg-bronze-500' : 'bg-green-500' }}" aria-hidden="true"></span>
                                    {{ $product['availability'] }}
                                </span>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Specifications group --}}
                    <tr>
                        <th colspan="{{ count($products) + 1 }}" scope="colgroup"
                            class="border-t border-navy-100 bg-navy-50 px-5 py-3 text-left font-display text-xs font-bold tracking-widest text-navy-700 uppercase">
                            Specifications
                        </th>
                    </tr>
                    @foreach ($specLabels as $key => $label)
                        <tr data-compare-row class="border-t border-navy-100">
                            <th scope="row" class="sticky left-0 z-10 bg-surface p-5 text-left align-top font-semibold text-navy-900">{{ $label }}</th>
                            @foreach ($products as $index => $product)
                                <td data-col="{{ $index }}" data-compare-value class="p-5 align-top text-navy-600">{{ $product['specs'][$key] }}</td>
                            @endforeach
                        </tr>
                    @endforeach

                    {{-- Materials --}}
                    <tr>
                        <th colspan="{{ count($products) + 1 }}" scope="colgroup"
                            class="border-t border-navy-100 bg-navy-50 px-5 py-3 text-left font-display text-xs font-bold tracking-widest text-navy-700 uppercase">
                            Materials
                        </th>
                    </tr>
                    <tr data-compare-row class="border-t border-navy-100">
                        <th scope="row" class="sticky left-0 z-10 bg-surface p-5 text-left align-top font-semibold text-navy-900">Construction</th>
                        @foreach ($products as $index => $product)
                            <td data-col="{{ $index }}" data-compare-value class="p-5 align-top leading-relaxed text-navy-600">{{ $product['materials'] }}</td>
                        @endforeach
                    </tr>

                    {{-- Warranty --}}
                    <tr data-compare-row class="border-t border-navy-100">
                        <th scope="row" class="sticky left-0 z-10 bg-surface p-5 text-left align-top font-semibold text-navy-900">Warranty</th>
                        @foreach ($products as $index => $product)
                            <td data-col="{{ $index }}" data-compare-value class="p-5 align-top">
                                <span class="inline-flex items-center gap-2 text-navy-700">
                                    <svg class="size-4 shrink-0 text-olive-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z"/>
                                    </svg>
                                    {{ $product['warranty'] }}
                                </span>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Features group --}}
                    <tr>
                        <th colspan="{{ count($products) + 1 }}" scope="colgroup"
                            class="border-t border-navy-100 bg-navy-50 px-5 py-3 text-left font-display text-xs font-bold tracking-widest text-navy-700 uppercase">
                            Features
                        </th>
                    </tr>
                    @foreach ($featureLabels as $key => $label)
                        <tr data-compare-row class="border-t border-navy-100">
                            <th scope="row" class="sticky left-0 z-10 bg-surface p-5 text-left align-top font-semibold text-navy-900">{{ $label }}</th>
                            @foreach ($products as $index => $product)
                                <td data-col="{{ $index }}" data-compare-value class="p-5 align-top">
                                    @if ($product['features'][$key])
                                        <span class="inline-flex size-7 items-center justify-center rounded-full bg-green-100 text-green-700">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                        </span>
                                        <span class="sr-only">Yes</span>
                                    @else
                                        <span class="inline-flex size-7 items-center justify-center rounded-full bg-navy-100 text-navy-400">
                                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                                        </span>
                                        <span class="sr-only">No</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    {{-- CTA row --}}
                    <tr class="border-t border-navy-100">
                        <th scope="row" class="sticky left-0 z-10 bg-surface p-5"><span class="sr-only">Actions</span></th>
                        @foreach ($products as $index => $product)
                            <td data-col="{{ $index }}" class="p-5 align-top">
                                <div class="flex flex-col gap-2">
                                    <x-ui.button variant="accent" size="sm">Add to cart</x-ui.button>
                                    <x-ui.button :href="route('product.show')" variant="outline" size="sm">View details</x-ui.button>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Empty state --}}
        <div data-compare-empty hidden class="mx-auto max-w-md py-20 text-center">
            <svg class="mx-auto h-40 w-auto" viewBox="0 0 220 150" fill="none" aria-hidden="true">
                <circle cx="110" cy="72" r="54" class="fill-navy-100"/>
                <rect x="66" y="46" width="40" height="56" rx="8" class="fill-white stroke-navy-900" stroke-width="3.5"/>
                <rect x="114" y="46" width="40" height="56" rx="8" class="fill-white stroke-navy-300" stroke-width="3.5" stroke-dasharray="6 6"/>
                <path d="M80 68h12M80 78h12M128 68h12M128 78h12" class="stroke-navy-300" stroke-width="3" stroke-linecap="round"/>
                <path d="M170 34c3.3 0 6-2.7 6-6 0 3.3 2.7 6 6 6-3.3 0-6 2.7-6 6 0-3.3-2.7-6-6-6Z" class="fill-bronze-400"/>
                <circle cx="52" cy="108" r="3.5" class="fill-olive-300"/>
            </svg>
            <h2 class="mt-6 font-display text-2xl font-bold text-navy-900">Nothing to compare</h2>
            <p class="mt-3 leading-relaxed text-navy-600">
                Add at least two products to see them side by side. Look for the compare icon on any product card.
            </p>
            <x-ui.button :href="route('shop')" variant="secondary" class="mt-8">Browse the shop</x-ui.button>
        </div>
    </div>
</x-layouts.app>
