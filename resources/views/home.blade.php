@php
    $enabled = collect($enabledSections ?? []);
    $categoryIcons = [
        'apparel' => 'M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z',
        'military-collectibles' => 'M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 0 2.5 6.5L12 20l-2.5 1.5L12 15Z',
        'outdoor-gear' => 'M12 3 3 20h18L12 3Zm0 5 5 9H7l5-9Z',
        'flags' => 'M5 3v18M5 4h13l-2.5 4L18 12H5',
        'challenge-coins' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0-4a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z',
        'books' => 'M5 4h11a3 3 0 0 1 3 3v13H8a3 3 0 0 1-3-3V4Zm0 13h14M9 8h6',
        'accessories' => 'M12 8a4 4 0 0 1 4 4v0a4 4 0 0 1-8 0v0a4 4 0 0 1 4-4Zm-2-5h4l1 5H9l1-5Zm0 18h4l1-5H9l1 5Z',
        'home-decor' => 'M3 11 12 3l9 8M6 10v10h12V10',
    ];
@endphp

<x-layouts.app
    :description="$homepageSettings->meta_description"
>

    @if ($enabled->contains('hero'))
        <x-ui.hero-slider :banners="$heroBanners" />
    @endif

    @if ($enabled->contains('flash_sale') && $countdownPromotion)
        <section class="border-b border-bronze-500/20 bg-navy-900" data-reveal>
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-4 py-8 sm:flex-row sm:px-6 lg:px-8">
                <div class="text-center sm:text-left">
                    <p class="text-sm font-semibold tracking-widest text-bronze-400 uppercase">Limited time</p>
                    <h2 class="mt-1 font-display text-xl font-bold text-white sm:text-2xl">{{ $countdownPromotion->headline }}</h2>
                    @if ($countdownPromotion->subheadline)
                        <p class="mt-2 text-sm text-navy-300">{{ $countdownPromotion->subheadline }}</p>
                    @endif
                </div>

                <div class="flex flex-col items-center gap-4 sm:flex-row">
                    <x-ui.countdown :ends-at="$countdownPromotion->ends_at" />

                    @if ($countdownPromotion->cta_label && $countdownPromotion->cta_url)
                        <x-ui.button :href="$countdownPromotion->cta_url" variant="accent" size="sm">
                            {{ $countdownPromotion->cta_label }}
                        </x-ui.button>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($enabled->contains('categories') && $categories->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28" data-reveal>
            <x-ui.section-heading
                eyebrow="Shop by category"
                title="Equipped for every mission"
                subtitle="Collections built to one standard — field-grade quality that honors the craft."
            />

            <div class="mt-12 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($categories as $category)
                    <x-ui.category-card
                        :name="$category->name"
                        :count="(int) $category->products_count"
                        :href="route('shop', ['category' => [$category->slug]])"
                        :icon="$categoryIcons[$category->slug] ?? 'M3 11 12 3l9 8M6 10v10h12V10'"
                        :image="$category->imageUrl()"
                    />
                @endforeach
            </div>
        </section>
    @endif

    @if ($enabled->contains('featured_collections'))
        <section class="bg-surface py-20 lg:py-28" data-reveal>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-ui.section-heading
                    eyebrow="Featured collections"
                    title="Curated for the season"
                />

                <div class="mt-12 grid grid-cols-1 gap-6 lg:grid-cols-3">
                    @forelse ($featuredCollections as $index => $collection)
                        @php
                            $collectionImage = $collection->bannerUrl()
                                ?? $collection->imageUrl()
                                ?? 'https://images.unsplash.com/photo-1501554728187-ce583db33af7?w=900&q=70&auto=format&fit=crop';
                        @endphp
                        <a href="{{ route('collections.show', $collection) }}" @class([
                            'group relative flex min-h-96 flex-col justify-end overflow-hidden rounded-card shadow-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover',
                            'lg:row-span-2 lg:min-h-[42rem]' => $index === 0,
                        ])>
                            <img src="{{ $collectionImage }}" alt="{{ $collection->name }}" loading="lazy"
                                 class="absolute inset-0 size-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                            <span class="absolute inset-0 bg-linear-to-t from-navy-950/85 via-navy-950/25 to-transparent" aria-hidden="true"></span>
                            <span class="relative p-8">
                                <span class="block font-display text-2xl font-bold text-white">{{ $collection->name }}</span>
                                @if ($collection->description)
                                    <span class="mt-2 block text-sm text-navy-200">{{ $collection->description }}</span>
                                @endif
                                <span class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition-colors duration-300 group-hover:bg-bronze-500">
                                    Explore collection
                                    <svg class="size-4 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                                </span>
                            </span>
                        </a>
                    @empty
                        <p class="col-span-full text-center text-navy-600">Featured collections will appear here once published.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    @if ($enabled->contains('featured_products') && $featuredProducts->isNotEmpty())
        <section id="featured-products" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28" data-reveal>
            <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                <x-ui.section-heading
                    align="left"
                    eyebrow="Featured products"
                    title="Handpicked for the season"
                    subtitle="Published, in-stock products selected by our team."
                />
                <x-ui.button :href="route('shop', ['featured' => 1])" variant="outline">View all featured</x-ui.button>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredProducts as $product)
                    @php $badge = $product->shopBadge(); @endphp
                    <x-ui.product-card
                        :name="$product->name"
                        :category="$product->category?->name"
                        :price="$product->formattedPrice()"
                        :old-price="$product->isOnSale() ? $product->formattedCompareAtPrice() : null"
                        :badge="$badge['badge']"
                        :badge-variant="$badge['variant']"
                        :rating="$product->placeholderRating()"
                        :reviews="$product->placeholderReviewCount()"
                        :stock="$product->shopStockLabel()"
                        :stock-percent="$product->shopStockPercent()"
                        :image="$product->primaryImageUrl()"
                        :href="route('product.show', $product)"
                        :product-id="$product->id"
                    />
                @endforeach
            </div>
        </section>
    @endif

    @if ($enabled->contains('new_arrivals') && $newArrivals->isNotEmpty())
        <section id="new-arrivals" class="bg-surface py-20 lg:py-28" data-reveal>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                    <x-ui.section-heading
                        align="left"
                        eyebrow="New arrivals"
                        title="Just landed"
                        subtitle="Fresh drops from the workshop floor."
                    />
                    <x-ui.button :href="route('shop', ['new_arrival' => 1])" variant="outline">Shop new arrivals</x-ui.button>
                </div>

                <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($newArrivals as $product)
                        @php $badge = $product->shopBadge(); @endphp
                        <x-ui.product-card
                            :name="$product->name"
                            :category="$product->category?->name"
                            :price="$product->formattedPrice()"
                            :old-price="$product->isOnSale() ? $product->formattedCompareAtPrice() : null"
                            :badge="$badge['badge']"
                            :badge-variant="$badge['variant']"
                            :rating="$product->placeholderRating()"
                            :reviews="$product->placeholderReviewCount()"
                            :stock="$product->shopStockLabel()"
                            :stock-percent="$product->shopStockPercent()"
                            :image="$product->primaryImageUrl()"
                            :href="route('product.show', $product)"
                            :product-id="$product->id"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($enabled->contains('best_sellers'))
        <section id="best-sellers" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28" data-reveal>
            <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                <x-ui.section-heading
                    align="left"
                    eyebrow="Best sellers"
                    title="Trusted by the community"
                    subtitle="Ranked from completed customer orders — not hardcoded picks."
                />
                <x-ui.button :href="route('shop')" variant="outline">View all products</x-ui.button>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($bestSellers as $product)
                    @php $badge = $product->shopBadge(); @endphp
                    <x-ui.product-card
                        :name="$product->name"
                        :category="$product->category?->name"
                        :price="$product->formattedPrice()"
                        :old-price="$product->isOnSale() ? $product->formattedCompareAtPrice() : null"
                        :badge="$badge['badge']"
                        :badge-variant="$badge['variant']"
                        :rating="$product->placeholderRating()"
                        :reviews="$product->placeholderReviewCount()"
                        :stock="$product->shopStockLabel()"
                        :stock-percent="$product->shopStockPercent()"
                        :image="$product->primaryImageUrl()"
                        :href="route('product.show', $product)"
                        :product-id="$product->id"
                    />
                @empty
                    <p class="col-span-full text-center text-navy-600">Best sellers will appear once delivered orders are recorded.</p>
                @endforelse
            </div>
        </section>
    @endif

    @if ($enabled->contains('promo_banners') && $promoBanners->isNotEmpty())
        <x-ui.promo-banner :banners="$promoBanners" />
    @endif

    @if ($enabled->contains('why_shop') && $whyShopFeatures->isNotEmpty())
        <section class="bg-navy-900 py-20 lg:py-24" data-reveal>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-semibold tracking-widest text-bronze-400 uppercase">Why shop with us</p>
                    <h2 class="mt-3 font-display text-3xl font-bold text-white sm:text-4xl">A standard worth defending</h2>
                </div>

                <div class="mt-14 grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach ($whyShopFeatures as $feature)
                        <div class="flex flex-col items-center text-center">
                            <span class="flex size-16 items-center justify-center rounded-2xl bg-white/5 text-bronze-400 ring-1 ring-white/10 transition-transform duration-300 hover:scale-110">
                                <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="{{ $feature->icon }}"/>
                                </svg>
                            </span>
                            <h3 class="mt-5 font-display text-base font-bold text-white">{{ $feature->title }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-navy-300">{{ $feature->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Featured veteran story — preserved static brand story --}}
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
                    <x-ui.button :href="route('about')" variant="secondary">Read the full story</x-ui.button>
                    <x-ui.button :href="route('about') . '#team'" variant="ghost">
                        Meet the team
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>

    @if ($saleProducts->isNotEmpty())
        <section class="relative overflow-hidden bg-navy-950 py-20 lg:py-28" data-reveal>
            <div class="absolute -top-32 right-0 size-96 rounded-full bg-bronze-500/10 blur-3xl" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold tracking-widest text-bronze-400 uppercase">On sale</p>
                        <h2 class="mt-3 font-display text-3xl font-bold text-white sm:text-4xl">Limited-time deals</h2>
                        <p class="mt-4 text-lg text-navy-300">Products currently discounted across the catalog.</p>
                    </div>
                    <x-ui.button :href="route('shop', ['on_sale' => 1])" variant="accent">Shop sale</x-ui.button>
                </div>

                <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($saleProducts->take(3) as $product)
                        <a href="{{ route('product.show', $product) }}" class="group overflow-hidden rounded-card bg-white/5 ring-1 ring-white/10 transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-white/10">
                            <span class="relative block aspect-[4/3] overflow-hidden">
                                <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}" loading="lazy"
                                     class="size-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                                <span class="absolute top-3 left-3">
                                    <x-ui.badge variant="bronze">-{{ $product->discountPercent() }}%</x-ui.badge>
                                </span>
                            </span>
                            <span class="block p-6">
                                <span class="block font-display text-lg font-bold text-white">{{ $product->name }}</span>
                                <span class="mt-1 block text-sm text-navy-300">{{ $product->category?->name }}</span>
                                <span class="mt-4 block text-lg font-bold text-bronze-400">{{ $product->formattedPrice() }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($enabled->contains('reviews') && $reviews->isNotEmpty())
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
                    @foreach ($reviews as $review)
                        <x-ui.review-card
                            :author="$review->author_name"
                            :role="$review->title"
                            :rating="$review->rating"
                            :body="$review->body"
                            :initials="$review->initials()"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($enabled->contains('brands') && $brands->isNotEmpty())
        <section class="border-y border-navy-100 bg-surface py-14" data-reveal>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm font-semibold tracking-widest text-navy-400 uppercase">Trusted brands</p>
                <ul class="mt-8 flex flex-wrap items-center justify-center gap-x-14 gap-y-6">
                    @foreach ($brands as $brand)
                        <x-ui.brand-logo
                            :name="$brand->name"
                            :logo="$brand->logoUrl()"
                            :href="route('shop', ['brand' => [$brand->slug]])"
                        />
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($showNewsletter ?? true)
        <x-ui.newsletter class="mt-16 mb-8" />
    @endif
</x-layouts.app>
