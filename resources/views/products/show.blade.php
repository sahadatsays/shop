@php
    /** @var array{label: string, gradient: string, icon: string} $galleryArt */
    $galleryArt = [
        1 => ['label' => 'Front view', 'gradient' => 'from-navy-200 via-navy-100 to-bronze-100'],
        2 => ['label' => 'Back view', 'gradient' => 'from-olive-200 via-olive-100 to-navy-100'],
        3 => ['label' => 'Detail — stitching', 'gradient' => 'from-bronze-200 via-bronze-100 to-navy-100'],
        4 => ['label' => 'Worn — lifestyle', 'gradient' => 'from-navy-300 via-olive-100 to-bronze-100'],
    ];

    $colors = [
        ['name' => 'Olive Drab', 'class' => 'bg-olive-600'],
        ['name' => 'Coyote Brown', 'class' => 'bg-bronze-600'],
        ['name' => 'Midnight Navy', 'class' => 'bg-navy-900'],
        ['name' => 'Stone Gray', 'class' => 'bg-gray-400'],
    ];

    $sizes = ['S', 'M', 'L', 'XL', 'XXL'];

    $reviews = [
        ['name' => 'Marcus T.', 'rating' => 5, 'date' => 'June 28, 2026', 'title' => 'Best jacket I have owned since my service days', 'body' => 'The build quality is exceptional. Heavy-duty zippers, reinforced elbows, and the fit is true to size. Wore it through two weeks of rain in the Cascades — kept me dry the entire time.', 'verified' => true],
        ['name' => 'Sarah K.', 'rating' => 5, 'date' => 'June 14, 2026', 'title' => 'Bought for my husband — he lives in it', 'body' => 'Gifted this for his retirement from the Corps. The attention to detail is obvious the moment you pick it up. The bronze hardware is a beautiful touch without being flashy.', 'verified' => true],
        ['name' => 'David R.', 'rating' => 4, 'date' => 'May 30, 2026', 'title' => 'Rugged and refined', 'body' => 'Excellent jacket overall. Runs slightly warm for summer use, but for three-season wear it is unbeatable. Knowing part of my purchase supports veteran programs makes it even better.', 'verified' => false],
    ];

    $relatedProducts = [
        ['name' => 'Patriot Canvas Rucksack', 'category' => 'Outdoor Gear', 'price' => '$149.00', 'badge' => 'New', 'badgeVariant' => 'olive'],
        ['name' => 'Honor Leather Wallet', 'category' => 'Everyday Carry', 'price' => '$79.00', 'badge' => null, 'badgeVariant' => 'bronze'],
        ['name' => 'Service Insulated Bottle', 'category' => 'Outdoor Gear', 'price' => '$42.00', 'badge' => 'Limited', 'badgeVariant' => 'navy'],
        ['name' => 'Sentinel Field Watch', 'category' => 'Everyday Carry', 'price' => '$229.00', 'badge' => 'Best seller', 'badgeVariant' => 'bronze'],
    ];

    $recentlyViewed = [
        ['name' => 'Valor Wool Beanie', 'category' => 'Apparel', 'price' => '$34.00'],
        ['name' => 'Garrison Belt', 'category' => 'Everyday Carry', 'price' => '$58.00'],
        ['name' => 'Basecamp Enamel Mug', 'category' => 'Home & Office', 'price' => '$24.00'],
        ['name' => 'Recon Sunglasses', 'category' => 'Everyday Carry', 'price' => '$119.00'],
    ];
@endphp

<x-layouts.app title="Ranger Field Jacket" description="Field-grade waxed canvas jacket built by veterans. Weatherproof, garment-dyed, and backed by a lifetime craftsmanship warranty.">

    {{-- Breadcrumbs --}}
    <nav aria-label="Breadcrumb" class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
            <li><a href="{{ route('home') }}" class="transition-colors duration-200 hover:text-navy-900">Home</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="#" class="transition-colors duration-200 hover:text-navy-900">Apparel</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="#" class="transition-colors duration-200 hover:text-navy-900">Jackets</a></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" class="font-medium text-navy-900">Ranger Field Jacket</li>
        </ol>
    </nav>

    {{-- Product section --}}
    <section class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 py-10 sm:px-6 lg:grid-cols-2 lg:gap-16 lg:px-8" data-gallery data-product-page data-product-id="{{ $product->id }}">

        {{-- ============ Gallery ============ --}}
        <div class="flex flex-col gap-4 lg:sticky lg:top-24 lg:self-start">
            <div data-stage
                 class="group/stage relative aspect-square cursor-zoom-in overflow-hidden rounded-card bg-navy-100 shadow-card">

                {{-- Photo layers --}}
                @foreach ($galleryArt as $index => $art)
                    <div data-art="{{ $index }}"
                         @if ($index !== 1) hidden @endif
                         class="absolute inset-0 transition-transform duration-200 ease-out will-change-transform">
                        <div class="flex size-full flex-col items-center justify-center gap-4 bg-linear-to-br {{ $art['gradient'] }}">
                            <svg class="size-24 text-navy-400/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z"/>
                            </svg>
                            <p class="text-sm font-medium tracking-wide text-navy-500 uppercase">{{ $art['label'] }}</p>
                        </div>
                    </div>
                @endforeach

                {{-- 360° layer --}}
                <div data-stage-360 hidden
                     class="absolute inset-0 cursor-grab touch-pan-y bg-linear-to-br from-navy-200 via-navy-100 to-olive-100 select-none active:cursor-grabbing"
                     style="perspective: 1200px">
                    <div data-spin-object class="flex size-full flex-col items-center justify-center gap-4 will-change-transform" style="transform-style: preserve-3d">
                        <svg class="size-24 text-navy-400/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z"/>
                        </svg>
                    </div>
                    <div class="pointer-events-none absolute inset-x-0 bottom-5 flex flex-col items-center gap-1.5">
                        <span data-spin-degrees class="rounded-full bg-navy-900/80 px-3 py-1 text-xs font-semibold text-white tabular-nums backdrop-blur-sm">0°</span>
                        <span class="flex items-center gap-1.5 text-xs font-medium text-navy-600">
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M8 12H2m0 0 3-3m-3 3 3 3M16 12h6m0 0-3-3m3 3-3 3"/>
                            </svg>
                            Drag to rotate
                        </span>
                    </div>
                </div>

                {{-- Overlays --}}
                <div class="pointer-events-none absolute top-4 left-4 z-10">
                    <x-ui.badge variant="danger">-24%</x-ui.badge>
                </div>
                <span data-zoom-hint
                      class="pointer-events-none absolute top-4 right-4 z-10 flex items-center gap-1.5 rounded-full bg-white/80 px-3 py-1.5 text-xs font-medium text-navy-700 opacity-0 backdrop-blur-sm transition-opacity duration-300 group-hover/stage:opacity-100">
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5M11 8v6M8 11h6"/>
                    </svg>
                    Hover to zoom
                </span>
            </div>

            {{-- Thumbnails --}}
            <div class="grid grid-cols-5 gap-3" role="tablist" aria-label="Product images">
                @foreach ($galleryArt as $index => $art)
                    <button type="button" data-thumb="{{ $index }}" role="tab"
                            aria-label="View image: {{ $art['label'] }}"
                            aria-selected="{{ $index === 1 ? 'true' : 'false' }}"
                            class="aspect-square overflow-hidden rounded-xl border-2 transition-all duration-200 {{ $index === 1 ? 'border-navy-900 shadow-soft' : 'border-transparent opacity-70 hover:opacity-100' }}">
                        <span class="flex size-full items-center justify-center bg-linear-to-br {{ $art['gradient'] }}">
                            <svg class="size-6 text-navy-400/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z"/>
                            </svg>
                        </span>
                    </button>
                @endforeach

                <button type="button" data-thumb-360 role="tab" aria-label="View 360 degree rotation" aria-selected="false"
                        class="flex aspect-square flex-col items-center justify-center gap-1 rounded-xl border-2 border-transparent bg-navy-900 text-white opacity-90 transition-all duration-200 hover:opacity-100">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 4a8 3.5 0 1 0 8 3.5M20 4v3.5h-3.5"/><circle cx="12" cy="14" r="5"/>
                    </svg>
                    <span class="text-[0.6rem] font-bold tracking-wider">360°</span>
                </button>
            </div>
        </div>

        {{-- ============ Product info ============ --}}
        <div class="flex flex-col">
            <p class="text-sm font-semibold tracking-widest text-bronze-600 uppercase">Valor Supply Co.</p>
            <h1 class="mt-2 font-display text-3xl font-bold text-navy-900 sm:text-4xl">Ranger Field Jacket</h1>

            {{-- Rating --}}
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <x-ui.rating :value="4.8" />
                <span class="text-sm font-semibold text-navy-900">4.8</span>
                <a href="#reviews" class="text-sm text-navy-500 underline-offset-4 transition-colors duration-200 hover:text-navy-900 hover:underline">132 reviews</a>
            </div>

            {{-- Price --}}
            <div class="mt-6 flex flex-wrap items-baseline gap-3">
                <span class="font-display text-3xl font-bold text-navy-900">$189.00</span>
                <span class="text-lg text-navy-400 line-through">$249.00</span>
                <x-ui.badge variant="danger">Save $60</x-ui.badge>
            </div>

            {{-- Availability + SKU --}}
            <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
                <span class="flex items-center gap-2 font-medium text-green-700">
                    <span class="relative flex size-2.5">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-green-500 opacity-60"></span>
                        <span class="relative inline-flex size-2.5 rounded-full bg-green-600"></span>
                    </span>
                    In stock — only 14 left
                </span>
                <span class="text-navy-500">SKU: <span class="font-medium text-navy-700">VS-RFJ-1042</span></span>
            </div>

            <hr class="mt-8 border-navy-100">

            {{-- Color picker --}}
            <fieldset class="mt-8" data-option-group>
                <legend class="text-sm font-semibold text-navy-900">
                    Color: <span data-option-label class="font-normal text-navy-600">Olive Drab</span>
                </legend>
                <div class="mt-3 flex flex-wrap gap-3">
                    @foreach ($colors as $index => $color)
                        <button type="button" data-option-value="{{ $color['name'] }}"
                                aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                                aria-label="Color: {{ $color['name'] }}"
                                class="flex size-11 items-center justify-center rounded-full ring-2 ring-offset-2 transition-all duration-200 {{ $index === 0 ? 'ring-navy-900' : 'ring-transparent hover:ring-navy-300' }}">
                            <span class="size-9 rounded-full {{ $color['class'] }} shadow-inner"></span>
                        </button>
                    @endforeach
                </div>
            </fieldset>

            {{-- Size picker --}}
            <fieldset class="mt-8" data-option-group>
                <div class="flex items-center justify-between">
                    <legend class="text-sm font-semibold text-navy-900">
                        Size: <span data-option-label class="font-normal text-navy-600">M</span>
                    </legend>
                    <a href="#" class="text-sm text-olive-700 underline-offset-4 transition-colors duration-200 hover:text-olive-800 hover:underline">Size guide</a>
                </div>
                <div class="mt-3 flex flex-wrap gap-2.5">
                    @foreach ($sizes as $size)
                        <button type="button" data-option-value="{{ $size }}"
                                aria-pressed="{{ $size === 'M' ? 'true' : 'false' }}"
                                class="min-w-14 rounded-xl border px-4 py-2.5 text-sm font-semibold transition-all duration-200 {{ $size === 'M' ? 'border-navy-900 bg-navy-900 text-white shadow-soft' : 'border-navy-200 bg-surface text-navy-700 hover:border-navy-400' }}">
                            {{ $size }}
                        </button>
                    @endforeach
                </div>
            </fieldset>

            {{-- Quantity + CTAs --}}
            <div class="mt-8" data-atc-anchor>
                <div class="flex flex-wrap items-stretch gap-3">
                    <div class="flex items-center rounded-xl border border-navy-200 bg-surface" data-quantity>
                        <button type="button" data-qty-minus aria-label="Decrease quantity"
                                class="flex size-12 items-center justify-center rounded-l-xl text-navy-600 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-900">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>
                        </button>
                        <input type="number" data-qty-input value="1" min="1" max="99" inputmode="numeric" aria-label="Quantity"
                               class="w-12 border-0 bg-transparent text-center text-sm font-semibold text-navy-900 [appearance:textfield] focus:outline-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                        <button type="button" data-qty-plus aria-label="Increase quantity"
                                class="flex size-12 items-center justify-center rounded-r-xl text-navy-600 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-900">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>

                    <x-ui.button variant="accent" class="flex-1" data-add-to-cart data-product-id="{{ $product->id }}">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                        </svg>
                        Add to cart
                    </x-ui.button>
                </div>

                <div class="mt-3 flex flex-wrap items-stretch gap-3">
                    <x-ui.button variant="primary" class="flex-1">Buy it now</x-ui.button>
                    <x-ui.button variant="outline" aria-label="Add to wishlist" data-toggle-active class="px-4">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 21s-7.5-4.7-9.5-9A5.5 5.5 0 0 1 12 6.5 5.5 5.5 0 0 1 21.5 12c-2 4.3-9.5 9-9.5 9Z"/>
                        </svg>
                    </x-ui.button>
                    <x-ui.button variant="outline" aria-label="Add to compare" data-toggle-active class="px-4">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 3v18M15 3v18M3 9h18M3 15h18"/>
                        </svg>
                    </x-ui.button>
                </div>
            </div>

            {{-- Shipping information --}}
            <div class="mt-8 space-y-3 rounded-card bg-olive-50 p-5">
                <p class="flex items-start gap-3 text-sm text-navy-700">
                    <svg class="mt-0.5 size-5 shrink-0 text-olive-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17.5" cy="18" r="1.6"/>
                    </svg>
                    <span><strong class="font-semibold text-navy-900">Free express shipping</strong> on this item. Order within <strong class="font-semibold text-olive-800">5 hrs 32 min</strong> and it arrives between <strong class="font-semibold text-navy-900">Thu, Jul 16</strong> and <strong class="font-semibold text-navy-900">Mon, Jul 20</strong>.</span>
                </p>
                <p class="flex items-start gap-3 text-sm text-navy-700">
                    <svg class="mt-0.5 size-5 shrink-0 text-olive-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 12a9 9 0 1 0 3-6.7M3 4v4h4"/>
                    </svg>
                    <span>30-day hassle-free returns and a <strong class="font-semibold text-navy-900">lifetime craftsmanship warranty</strong>.</span>
                </p>
            </div>

            {{-- Secure payment badges --}}
            <div class="mt-6">
                <p class="flex items-center gap-2 text-xs font-semibold tracking-wide text-navy-500 uppercase">
                    <svg class="size-4 text-olive-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                    </svg>
                    Guaranteed safe &amp; secure checkout
                </p>
                <ul class="mt-3 flex flex-wrap gap-2">
                    @foreach (['VISA', 'Mastercard', 'AMEX', 'PayPal', 'Apple Pay', 'G Pay'] as $method)
                        <li class="rounded-lg border border-navy-200 bg-surface px-3 py-1.5 text-xs font-bold tracking-wide text-navy-700">{{ $method }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Accordion --}}
            <div class="mt-10 border-t border-navy-100">
                <x-ui.accordion-item title="Description" :open="true">
                    <p>The Ranger Field Jacket is our flagship outerwear piece — a modern reinterpretation of the classic M-65, built from 10 oz waxed organic canvas and lined with brushed cotton twill. Designed by a team of Army and Marine Corps veterans, every seam is placed with purpose: articulated elbows for range of motion, a storm flap that actually stops wind, and pockets positioned where your hands expect them.</p>
                    <p class="mt-3">Garment-dyed and stone-washed for a broken-in feel from day one, it only gets better with wear. Five percent of every purchase funds veteran career transition programs.</p>
                </x-ui.accordion-item>

                <x-ui.accordion-item title="Specifications">
                    <dl class="grid grid-cols-1 gap-x-8 gap-y-3 sm:grid-cols-2">
                        @foreach ([
                            'Weight' => '1.4 kg / 3.1 lb',
                            'Shell' => '10 oz waxed organic canvas',
                            'Lining' => 'Brushed cotton twill',
                            'Hardware' => 'Antique bronze YKK zippers',
                            'Pockets' => '6 external, 2 internal',
                            'Fit' => 'Regular — true to size',
                            'Origin' => 'Cut & sewn in the USA',
                            'Warranty' => 'Lifetime craftsmanship',
                        ] as $spec => $specValue)
                            <div class="flex justify-between gap-4 border-b border-navy-50 pb-2">
                                <dt class="font-medium text-navy-900">{{ $spec }}</dt>
                                <dd class="text-right">{{ $specValue }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </x-ui.accordion-item>

                <x-ui.accordion-item title="Materials">
                    <ul class="list-inside list-disc space-y-2">
                        <li>100% organic cotton canvas shell, paraffin-wax impregnated for weather resistance</li>
                        <li>100% cotton twill lining, brushed for warmth and comfort</li>
                        <li>Solid antique bronze hardware — no plated alloys</li>
                        <li>Corozo nut buttons, hand-finished</li>
                        <li>Bluesign-certified dyes, free of PFAS and PFC coatings</li>
                    </ul>
                </x-ui.accordion-item>

                <x-ui.accordion-item title="Care Instructions">
                    <ul class="list-inside list-disc space-y-2">
                        <li>Do not machine wash — spot clean with cold water and a soft brush</li>
                        <li>Air dry away from direct heat; never tumble dry</li>
                        <li>Re-wax annually with the included wax bar for continued weather resistance</li>
                        <li>Store on a wide hanger in a dry place during the off-season</li>
                    </ul>
                </x-ui.accordion-item>
            </div>
        </div>
    </section>

    {{-- ============ Customer reviews ============ --}}
    <section id="reviews" class="bg-surface py-20" data-reveal>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-ui.section-heading align="left" eyebrow="Customer reviews" title="What the community says" />

            <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-3">
                {{-- Summary --}}
                <div class="rounded-card bg-canvas p-8 lg:self-start">
                    <div class="flex items-baseline gap-3">
                        <span class="font-display text-5xl font-extrabold text-navy-900">4.8</span>
                        <span class="text-navy-500">out of 5</span>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <x-ui.rating :value="4.8" />
                        <span class="text-sm text-navy-500">132 reviews</span>
                    </div>
                    <div class="mt-6 space-y-2.5">
                        @foreach ([5 => 84, 4 => 12, 3 => 3, 2 => 1, 1 => 0] as $stars => $sharePercent)
                            <div class="flex items-center gap-3 text-sm">
                                <span class="w-8 shrink-0 font-medium text-navy-700">{{ $stars }} ★</span>
                                <div class="h-2 flex-1 overflow-hidden rounded-full bg-navy-100">
                                    <div class="h-full rounded-full bg-bronze-500" style="width: {{ $sharePercent }}%"></div>
                                </div>
                                <span class="w-10 shrink-0 text-right text-navy-500">{{ $sharePercent }}%</span>
                            </div>
                        @endforeach
                    </div>
                    <x-ui.button variant="outline" class="mt-8 w-full">Write a review</x-ui.button>
                </div>

                {{-- Review cards --}}
                <div class="space-y-6 lg:col-span-2">
                    @foreach ($reviews as $review)
                        <article class="rounded-card bg-canvas p-6 sm:p-8">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-10 items-center justify-center rounded-full bg-navy-900 font-display text-sm font-bold text-bronze-400">
                                        {{ mb_substr($review['name'], 0, 1) }}
                                    </span>
                                    <div>
                                        <p class="flex items-center gap-2 text-sm font-semibold text-navy-900">
                                            {{ $review['name'] }}
                                            @if ($review['verified'])
                                                <x-ui.badge variant="success">
                                                    <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                                    Verified buyer
                                                </x-ui.badge>
                                            @endif
                                        </p>
                                        <p class="text-xs text-navy-500">{{ $review['date'] }}</p>
                                    </div>
                                </div>
                                <x-ui.rating :value="$review['rating']" size="sm" />
                            </div>
                            <h3 class="mt-4 font-display text-base font-semibold text-navy-900">{{ $review['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-navy-600">{{ $review['body'] }}</p>
                        </article>
                    @endforeach

                    <x-ui.button variant="ghost" class="mx-auto flex">
                        Load more reviews
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ Related products ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8" data-reveal>
        <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
            <x-ui.section-heading align="left" eyebrow="You may also like" title="Completes the kit" />
            <x-ui.button href="#" variant="outline">View all</x-ui.button>
        </div>
        <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            @foreach ($relatedProducts as $relatedItem)
                <x-ui.product-card
                    :name="$relatedItem['name']"
                    :category="$relatedItem['category']"
                    :price="$relatedItem['price']"
                    :badge="$relatedItem['badge']"
                    :badge-variant="$relatedItem['badgeVariant']"
                    :href="route('product.show')"
                />
            @endforeach
        </div>
    </section>

    {{-- ============ Recently viewed ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-4 sm:px-6 lg:px-8" data-reveal>
        <x-ui.section-heading align="left" eyebrow="Pick up where you left off" title="Recently viewed" />
        <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            @foreach ($recentlyViewed as $viewedItem)
                <x-ui.product-card
                    :name="$viewedItem['name']"
                    :category="$viewedItem['category']"
                    :price="$viewedItem['price']"
                    :href="route('product.show')"
                />
            @endforeach
        </div>
    </section>

    {{-- ============ Sticky add-to-cart bar ============ --}}
    <div data-sticky-atc
         class="glass fixed inset-x-0 bottom-0 z-40 translate-y-full border-t border-navy-900/5 transition-transform duration-300 ease-out"
         aria-hidden="true">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
                <span class="hidden size-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-linear-to-br from-navy-200 via-navy-100 to-bronze-100 sm:flex">
                    <svg class="size-6 text-navy-400/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z"/>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="truncate font-display text-sm font-semibold text-navy-900">{{ $product->name }}</p>
                    <p class="text-sm text-navy-600">
                        <span class="font-bold text-navy-900">{{ $product->formattedPrice() }}</span>
                        @if ($product->isOnSale())
                            <span class="text-navy-400 line-through">{{ $product->formattedCompareAtPrice() }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <x-ui.button variant="accent" size="sm" class="shrink-0" data-add-to-cart data-product-id="{{ $product->id }}">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                </svg>
                Add to cart
            </x-ui.button>
        </div>
    </div>
</x-layouts.app>
