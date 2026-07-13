@php
    $bestSellers = [
        ['name' => 'Ranger Field Jacket', 'category' => 'Apparel', 'price' => '$189.00', 'oldPrice' => '$249.00', 'badge' => '-24%', 'badgeVariant' => 'danger', 'rating' => 4.8, 'reviews' => 132, 'stock' => 'Only 14 left — order soon', 'stockPercent' => 18, 'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Patriot Canvas Rucksack', 'category' => 'Outdoor Gear', 'price' => '$149.00', 'oldPrice' => null, 'badge' => 'Best seller', 'badgeVariant' => 'bronze', 'rating' => 4.9, 'reviews' => 87, 'stock' => 'In stock', 'stockPercent' => null, 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Sentinel Field Watch', 'category' => 'Everyday Carry', 'price' => '$229.00', 'oldPrice' => '$279.00', 'badge' => '-18%', 'badgeVariant' => 'danger', 'rating' => 4.7, 'reviews' => 64, 'stock' => 'In stock', 'stockPercent' => null, 'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Honor EDC Kit', 'category' => 'Everyday Carry', 'price' => '$96.00', 'oldPrice' => null, 'badge' => 'New', 'badgeVariant' => 'olive', 'rating' => 4.6, 'reviews' => 41, 'stock' => 'Only 9 left — order soon', 'stockPercent' => 12, 'image' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=800&q=70&auto=format&fit=crop'],
    ];

    $limitedEdition = [
        ['name' => "Founder's Challenge Coin — No. 001 Series", 'price' => '$65.00', 'made' => 'Individually numbered, 500 minted', 'image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Anniversary Stitched Flag', 'price' => '$120.00', 'made' => 'Hand-sewn embroidered stars', 'image' => 'https://images.unsplash.com/photo-1520095972714-909e91b038e5?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Heritage Automatic Watch', 'price' => '$449.00', 'made' => 'Sapphire crystal, 200 pieces', 'image' => 'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=800&q=70&auto=format&fit=crop'],
    ];

    $customerReviews = [
        ['name' => 'Marcus T.', 'role' => 'Army veteran, 2011–2019', 'rating' => 5, 'body' => 'The Ranger jacket is the best piece of kit I have owned since my service days. Quality you can feel in every seam.'],
        ['name' => 'Sarah K.', 'role' => 'Military spouse', 'rating' => 5, 'body' => 'Bought my husband the rucksack for his retirement. He inspects everything — this passed on the first look.'],
        ['name' => 'David R.', 'role' => 'Marine Corps veteran', 'rating' => 5, 'body' => 'Fast shipping, honest sizing, and the profits give back to the community. This is how a store should be run.'],
        ['name' => 'Elena M.', 'role' => 'Air Force, active duty', 'rating' => 4, 'body' => 'The challenge coins are stunning — heavy, detailed, and beautifully finished. My squadron ordered a full set.'],
        ['name' => 'James W.', 'role' => 'Navy veteran, 1998–2010', 'rating' => 5, 'body' => 'Lifetime warranty is not marketing talk here. They repaired my wallet stitching free of charge, three years in.'],
    ];

    $instagramShots = [
        ['image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&q=70&auto=format&fit=crop', 'alt' => 'Lake and mountains at golden hour'],
        ['image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=600&q=70&auto=format&fit=crop', 'alt' => 'Sunlight through a forest road'],
        ['image' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=600&q=70&auto=format&fit=crop', 'alt' => 'Canoe on a calm alpine lake'],
        ['image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600&q=70&auto=format&fit=crop', 'alt' => 'Snow-capped mountain range'],
        ['image' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=600&q=70&auto=format&fit=crop', 'alt' => 'Mountains under a starry night sky'],
        ['image' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=600&q=70&auto=format&fit=crop', 'alt' => 'Stack of well-read books'],
    ];
@endphp

<x-layouts.app>

    {{-- ============ Hero ============ --}}
    <section class="relative flex min-h-[85vh] items-center overflow-hidden bg-navy-950">
        <div class="absolute inset-0" aria-hidden="true">
            <img src="https://images.unsplash.com/photo-1508672019048-805c876b67e2?w=2000&q=75&auto=format&fit=crop"
                 alt="" fetchpriority="high"
                 class="size-full animate-slow-zoom object-cover opacity-60 will-change-transform">
            <div class="absolute inset-0 bg-linear-to-r from-navy-950 via-navy-950/70 to-navy-950/20"></div>
            <div class="absolute -bottom-40 -left-24 size-[30rem] rounded-full bg-olive-600/20 blur-3xl"></div>
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <div class="max-w-2xl animate-fade-in-up">
                <x-ui.badge variant="bronze" class="mb-6">
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z"/>
                    </svg>
                    Veteran owned &amp; operated since 2019
                </x-ui.badge>

                <h1 class="font-display text-4xl font-extrabold tracking-tight text-white sm:text-6xl lg:text-7xl">
                    Honor in every<br>
                    <span class="text-bronze-400">stitch and seam.</span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-relaxed text-navy-200">
                    Premium apparel, collectibles, and field gear designed by veterans who
                    hold their products to the same standard they held their service.
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <x-ui.button href="#best-sellers" variant="accent" size="lg">
                        Shop best sellers
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14m-6-6 6 6-6 6"/>
                        </svg>
                    </x-ui.button>
                    <x-ui.button href="#veteran-story" size="lg" class="border border-white/20 bg-white/10 text-white backdrop-blur-sm hover:bg-white/20">
                        Our story
                    </x-ui.button>
                </div>

                {{-- Trust badges --}}
                <dl class="mt-14 grid max-w-lg grid-cols-3 gap-6 border-t border-white/10 pt-8">
                    @foreach ([['38K+', 'Orders shipped'], ['4.9/5', 'Average rating'], ['$212K', 'Given back']] as [$stat, $statLabel])
                        <div>
                            <dt class="sr-only">{{ $statLabel }}</dt>
                            <dd class="font-display text-2xl font-bold text-white sm:text-3xl">{{ $stat }}</dd>
                            <dd class="mt-1 text-sm text-navy-300">{{ $statLabel }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- ============ Shop by category ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28" data-reveal>
        <x-ui.section-heading
            eyebrow="Shop by category"
            title="Equipped for every mission"
            subtitle="Eight collections, one standard — field-grade quality that honors the craft."
        />

        <div class="mt-12 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
            @foreach ([
                ['name' => 'Apparel', 'count' => 48, 'icon' => 'M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z'],
                ['name' => 'Military Collectibles', 'count' => 32, 'icon' => 'M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 0 2.5 6.5L12 20l-2.5 1.5L12 15Z'],
                ['name' => 'Outdoor Gear', 'count' => 36, 'icon' => 'M12 3 3 20h18L12 3Zm0 5 5 9H7l5-9Z'],
                ['name' => 'Flags', 'count' => 21, 'icon' => 'M5 3v18M5 4h13l-2.5 4L18 12H5'],
                ['name' => 'Challenge Coins', 'count' => 27, 'icon' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0-4a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z'],
                ['name' => 'Books', 'count' => 54, 'icon' => 'M5 4h11a3 3 0 0 1 3 3v13H8a3 3 0 0 1-3-3V4Zm0 13h14M9 8h6'],
                ['name' => 'Accessories', 'count' => 45, 'icon' => 'M12 8a4 4 0 0 1 4 4v0a4 4 0 0 1-8 0v0a4 4 0 0 1 4-4Zm-2-5h4l1 5H9l1-5Zm0 18h4l1-5H9l1 5Z'],
                ['name' => 'Home Decor', 'count' => 30, 'icon' => 'M3 11 12 3l9 8M6 10v10h12V10'],
            ] as $category)
                <a href="#" class="group flex flex-col items-start gap-4 rounded-card bg-surface p-6 shadow-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover sm:p-8">
                    <span class="flex size-14 items-center justify-center rounded-2xl bg-olive-100 text-olive-700 transition-all duration-300 group-hover:scale-110 group-hover:bg-olive-600 group-hover:text-white">
                        <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="{{ $category['icon'] }}"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block font-display text-base font-bold text-navy-900">{{ $category['name'] }}</span>
                        <span class="mt-1 block text-sm text-navy-500">{{ $category['count'] }} products</span>
                    </span>
                    <span class="mt-auto flex items-center gap-1.5 text-sm font-semibold text-bronze-600 opacity-0 transition-all duration-300 group-hover:opacity-100">
                        Shop now
                        <svg class="size-3.5 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ============ Featured collections ============ --}}
    <section class="bg-surface py-20 lg:py-28" data-reveal>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-ui.section-heading
                eyebrow="Featured collections"
                title="Curated for the season"
            />

            <div class="mt-12 grid grid-cols-1 gap-6 lg:grid-cols-3">
                @foreach ([
                    ['name' => 'The Expedition Collection', 'desc' => 'Packs, layers, and tools for the backcountry', 'cta' => 'Explore expedition', 'image' => 'https://images.unsplash.com/photo-1501554728187-ce583db33af7?w=900&q=70&auto=format&fit=crop', 'alt' => 'Hiker overlooking a mountain valley', 'tall' => true],
                    ['name' => 'Heritage Apparel', 'desc' => 'Garment-dyed classics, built to break in', 'cta' => 'Shop apparel', 'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=900&q=70&auto=format&fit=crop', 'alt' => 'Neatly folded garment-dyed apparel', 'tall' => false],
                    ['name' => 'Trail Ready', 'desc' => 'Boots and gear proven on hard miles', 'cta' => 'Gear up', 'image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=900&q=70&auto=format&fit=crop', 'alt' => 'Well-worn hiking boots on a trail', 'tall' => false],
                ] as $collection)
                    <a href="#" class="group relative flex min-h-96 flex-col justify-end overflow-hidden rounded-card shadow-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover">
                        <img src="{{ $collection['image'] }}" alt="{{ $collection['alt'] }}" loading="lazy"
                             class="absolute inset-0 size-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                        <span class="absolute inset-0 bg-linear-to-t from-navy-950/85 via-navy-950/25 to-transparent" aria-hidden="true"></span>
                        <span class="relative p-8">
                            <span class="block font-display text-2xl font-bold text-white">{{ $collection['name'] }}</span>
                            <span class="mt-2 block text-sm text-navy-200">{{ $collection['desc'] }}</span>
                            <span class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition-colors duration-300 group-hover:bg-bronze-500">
                                {{ $collection['cta'] }}
                                <svg class="size-4 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ Best sellers ============ --}}
    <section id="best-sellers" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28" data-reveal>
        <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
            <x-ui.section-heading
                align="left"
                eyebrow="Best sellers"
                title="Trusted by the community"
                subtitle="The gear our customers reorder, gift, and swear by."
            />
            <x-ui.button href="#" variant="outline">View all products</x-ui.button>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($bestSellers as $product)
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
    </section>

    {{-- ============ Why shop with us ============ --}}
    <section class="bg-navy-900 py-20 lg:py-24" data-reveal>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-semibold tracking-widest text-bronze-400 uppercase">Why shop with us</p>
                <h2 class="mt-3 font-display text-3xl font-bold text-white sm:text-4xl">A standard worth defending</h2>
            </div>

            <div class="mt-14 grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ([
                    ['title' => 'Authentic Products', 'desc' => 'Licensed, verified, and sourced with integrity', 'icon' => 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm-1.5 13.5-3-3 1.4-1.4 1.6 1.6 4.6-4.6 1.4 1.4-6 6Z'],
                    ['title' => 'Veteran Owned', 'desc' => 'Founded and run by those who served', 'icon' => 'M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 0 2.5 6.5L12 20l-2.5 1.5L12 15Z'],
                    ['title' => 'Fast Shipping', 'desc' => 'Free express delivery on orders over $75', 'icon' => 'M3 7h11v9H3zM14 10h4l3 3v3h-7zM7 19a1.6 1.6 0 1 0 0-3.2A1.6 1.6 0 0 0 7 19Zm10.5 0a1.6 1.6 0 1 0 0-3.2 1.6 1.6 0 0 0 0 3.2Z'],
                    ['title' => 'Secure Payment', 'desc' => '256-bit encrypted checkout, every order', 'icon' => 'M4 10h16v10H4zM8 10V7a4 4 0 0 1 8 0v3'],
                    ['title' => 'Lifetime Support', 'desc' => 'Craftsmanship warranty that outlasts trends', 'icon' => 'M12 21s-7.5-4.7-9.5-9A5.5 5.5 0 0 1 12 6.5 5.5 5.5 0 0 1 21.5 12c-2 4.3-9.5 9-9.5 9Z'],
                ] as $reason)
                    <div class="flex flex-col items-center text-center">
                        <span class="flex size-16 items-center justify-center rounded-2xl bg-white/5 text-bronze-400 ring-1 ring-white/10 transition-transform duration-300 hover:scale-110">
                            <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="{{ $reason['icon'] }}"/>
                            </svg>
                        </span>
                        <h3 class="mt-5 font-display text-base font-bold text-white">{{ $reason['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-navy-300">{{ $reason['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ Featured veteran story ============ --}}
    <section id="veteran-story" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28" data-reveal>
        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <div class="relative">
                <div class="overflow-hidden rounded-card shadow-card-hover">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=1000&q=70&auto=format&fit=crop"
                         alt="Portrait of company founder James Callahan" loading="lazy"
                         class="aspect-[4/5] w-full object-cover">
                </div>
                <div class="glass absolute -right-4 -bottom-6 max-w-56 rounded-card p-5 sm:-right-8">
                    <p class="font-display text-3xl font-extrabold text-navy-900">12 yrs</p>
                    <p class="mt-1 text-sm text-navy-600">of service before founding Valor Supply Co.</p>
                </div>
            </div>

            <div>
                <p class="text-sm font-semibold tracking-widest text-bronze-600 uppercase">Featured veteran story</p>
                <h2 class="mt-3 font-display text-3xl font-bold text-navy-900 sm:text-4xl">From the 75th Ranger Regiment to the workshop floor</h2>
                <p class="mt-6 text-lg leading-relaxed text-navy-600">
                    After three deployments and twelve years of service, founder James Callahan came home
                    to a simple frustration: nothing on the shelf was built the way his issued gear was.
                    So he rented a garage, bought a walking-foot sewing machine, and started making it himself.
                </p>
                <blockquote class="mt-8 border-l-4 border-bronze-500 pl-6">
                    <p class="font-display text-xl leading-relaxed font-semibold text-navy-900">
                        &ldquo;We don't sell nostalgia. We sell the standard we lived by — and we stand behind
                        every piece for life.&rdquo;
                    </p>
                    <footer class="mt-4 text-sm text-navy-500">James Callahan — Founder, U.S. Army Ret.</footer>
                </blockquote>
                <div class="mt-10 flex flex-wrap gap-4">
                    <x-ui.button href="#" variant="secondary">Read the full story</x-ui.button>
                    <x-ui.button href="#" variant="ghost">
                        Meet the team
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ Limited edition ============ --}}
    <section class="relative overflow-hidden bg-navy-950 py-20 lg:py-28" data-reveal>
        <div class="absolute -top-32 right-0 size-96 rounded-full bg-bronze-500/10 blur-3xl" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold tracking-widest text-bronze-400 uppercase">Limited edition</p>
                    <h2 class="mt-3 font-display text-3xl font-bold text-white sm:text-4xl">Once they're gone, they're gone</h2>
                    <p class="mt-4 text-lg text-navy-300">Small-batch releases, individually numbered and never reissued.</p>
                </div>
                <x-ui.button href="#" variant="accent">Shop limited drops</x-ui.button>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($limitedEdition as $product)
                    <a href="{{ route('product.show') }}" class="group overflow-hidden rounded-card bg-white/5 ring-1 ring-white/10 transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-white/10">
                        <span class="relative block aspect-[4/3] overflow-hidden">
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy"
                                 class="size-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                            <span class="absolute top-3 left-3">
                                <x-ui.badge variant="bronze">Limited</x-ui.badge>
                            </span>
                        </span>
                        <span class="block p-6">
                            <span class="block font-display text-lg font-bold text-white">{{ $product['name'] }}</span>
                            <span class="mt-1 block text-sm text-navy-300">{{ $product['made'] }}</span>
                            <span class="mt-4 block text-lg font-bold text-bronze-400">{{ $product['price'] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ Customer reviews carousel ============ --}}
    <section class="py-20 lg:py-28" data-reveal>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                <x-ui.section-heading
                    align="left"
                    eyebrow="Customer reviews"
                    title="Words from the ranks"
                />
                <div class="flex gap-2" data-carousel-controls>
                    <button type="button" data-carousel-prev aria-label="Previous reviews"
                            class="flex size-11 items-center justify-center rounded-full border border-navy-200 bg-surface text-navy-700 transition-all duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white disabled:opacity-40">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5m6 6-6-6 6-6"/></svg>
                    </button>
                    <button type="button" data-carousel-next aria-label="Next reviews"
                            class="flex size-11 items-center justify-center rounded-full border border-navy-200 bg-surface text-navy-700 transition-all duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white disabled:opacity-40">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </button>
                </div>
            </div>

            <div data-carousel class="scrollbar-none -mx-4 mt-12 flex snap-x snap-mandatory gap-6 overflow-x-auto scroll-smooth px-4 pb-4 sm:mx-0 sm:px-0"
                 aria-label="Customer reviews carousel">
                @foreach ($customerReviews as $review)
                    <article class="flex w-[85%] shrink-0 snap-start flex-col rounded-card bg-surface p-8 shadow-card sm:w-[46%] lg:w-[31.5%]">
                        <x-ui.rating :value="$review['rating']" />
                        <p class="mt-5 flex-1 text-base leading-relaxed text-navy-700">&ldquo;{{ $review['body'] }}&rdquo;</p>
                        <footer class="mt-6 flex items-center gap-3 border-t border-navy-100 pt-5">
                            <span class="flex size-11 items-center justify-center rounded-full bg-navy-900 font-display text-sm font-bold text-bronze-400">
                                {{ mb_substr($review['name'], 0, 1) }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-navy-900">{{ $review['name'] }}</p>
                                <p class="text-xs text-navy-500">{{ $review['role'] }}</p>
                            </div>
                        </footer>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ Brand partners ============ --}}
    <section class="border-y border-navy-100 bg-surface py-14" data-reveal>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm font-semibold tracking-widest text-navy-400 uppercase">Trusted by organizations that serve</p>
            <ul class="mt-8 flex flex-wrap items-center justify-center gap-x-14 gap-y-6">
                @foreach (['USO Alliance', 'Folds of Honor', 'Team Rubicon', 'Wounded Warrior', 'Hire Heroes', 'VFW Post 82'] as $partner)
                    <li class="font-display text-lg font-bold tracking-wide text-navy-300 transition-colors duration-300 select-none hover:text-navy-600">
                        {{ $partner }}
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ============ Instagram gallery ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28" data-reveal>
        <x-ui.section-heading
            eyebrow="@valorsupplyco"
            title="Field notes from the community"
            subtitle="Tag #ValorInTheField for a chance to be featured."
        />

        <div class="mt-12 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($instagramShots as $shot)
                <a href="#" class="group relative aspect-square overflow-hidden rounded-2xl" aria-label="Open Instagram post: {{ $shot['alt'] }}">
                    <img src="{{ $shot['image'] }}" alt="{{ $shot['alt'] }}" loading="lazy"
                         class="size-full object-cover transition-transform duration-500 ease-out group-hover:scale-110">
                    <span class="absolute inset-0 flex items-center justify-center bg-navy-950/50 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <svg class="size-7 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M8 3h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8a5 5 0 0 1 5-5Zm4 5.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7ZM17 6.8a.8.8 0 1 0 0 1.6.8.8 0 0 0 0-1.6Z"/>
                        </svg>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ============ Newsletter ============ --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="relative overflow-hidden rounded-card bg-linear-to-br from-olive-800 to-olive-600 px-6 py-16 shadow-card sm:px-12 lg:px-16">
            <div class="absolute -top-24 -right-24 size-72 rounded-full bg-bronze-400/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-xl text-center">
                <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">Join the ranks</h2>
                <p class="mt-4 text-lg text-olive-100">
                    Early access to limited drops, exclusive offers, and stories from the veteran community.
                </p>
                <form class="mt-8 flex flex-col gap-3 sm:flex-row" action="#" method="post">
                    <label for="newsletter-email" class="sr-only">Email address</label>
                    <input type="email" id="newsletter-email" name="email" required placeholder="Enter your email"
                           class="w-full rounded-xl border-0 bg-white/95 px-5 py-3.5 text-sm text-ink placeholder:text-navy-400 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-300">
                    <x-ui.button type="submit" variant="accent" class="shrink-0">Subscribe</x-ui.button>
                </form>
                <p class="mt-4 text-sm text-olive-200">No spam. Unsubscribe anytime.</p>
            </div>
        </div>
    </section>
</x-layouts.app>
