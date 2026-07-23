@php
    /** @var \App\Models\Product $product */
    /** @var list<array{label: string, url: string|null}> $breadcrumbs */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Product> $relatedProducts */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Product> $recentlyViewedProducts */

    $galleryImages = $product->images;
    $colors = $product->colorAttributes();
    $sizes = $product->sizeAttributes();
    $materials = $product->materialAttributes();
    $careItems = $product->careAttributes();
    $primaryImageUrl = $product->primaryImageUrl();
    $pageTitle = $product->meta_title ?: $product->name;
    $pageDescription = $product->meta_description ?: ($product->short_description ?: $product->name);
    $displayRating = $reviewSummary['average'] ?? $product->displayRating();
    $displayReviewCount = $reviewSummary['count'] > 0 ? $reviewSummary['count'] : $product->displayReviewCount();
    $thumbGridClass = match (min(max($galleryImages->count(), 1), 5)) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-2',
        3 => 'grid-cols-3',
        4 => 'grid-cols-4',
        default => 'grid-cols-5',
    };
@endphp

<x-layouts.app :title="$pageTitle" :description="$pageDescription">

    {{-- Breadcrumbs --}}
    <nav aria-label="Breadcrumb" class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
            @foreach ($breadcrumbs as $crumb)
                @if (! $loop->first)
                    <li aria-hidden="true">/</li>
                @endif
                <li>
                    @if ($crumb['url'])
                        <a href="{{ $crumb['url'] }}" class="transition-colors duration-200 hover:text-navy-900">{{ $crumb['label'] }}</a>
                    @else
                        <span aria-current="page" class="font-medium text-navy-900">{{ $crumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    {{-- Product section --}}
    <section class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 py-10 sm:px-6 lg:grid-cols-2 lg:gap-16 lg:px-8" data-gallery data-product-page data-product-id="{{ $product->id }}">

        {{-- ============ Gallery ============ --}}
        <div class="flex flex-col gap-4 lg:sticky lg:top-24 lg:self-start">
            <div data-stage
                 class="group/stage relative aspect-square cursor-zoom-in overflow-hidden rounded-card bg-navy-100 shadow-card">

                @if ($galleryImages->isNotEmpty())
                    @foreach ($galleryImages as $image)
                        <div data-art="{{ $loop->index }}"
                             @if (! $loop->first) hidden @endif
                             class="absolute inset-0 transition-transform duration-200 ease-out will-change-transform">
                            <img src="{{ $image->url() }}"
                                 alt="{{ $image->alt_text ?: $product->name }}"
                                 class="size-full object-cover">
                        </div>
                    @endforeach
                @else
                    <div data-art="0"
                         class="absolute inset-0 transition-transform duration-200 ease-out will-change-transform">
                        <div class="flex size-full flex-col items-center justify-center gap-4 bg-linear-to-br from-navy-200 via-navy-100 to-bronze-100">
                            <svg class="size-24 text-navy-400/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z"/>
                            </svg>
                            <p class="text-sm font-medium tracking-wide text-navy-500 uppercase">{{ $product->name }}</p>
                        </div>
                    </div>
                @endif

                @php
                    $galleryBadge = $product->isOnSale() && $product->discountPercent()
                        ? ['badge' => '-'.$product->discountPercent().'%', 'variant' => 'danger']
                        : $product->shopBadge();
                @endphp
                @if ($galleryBadge['badge'])
                    <div class="pointer-events-none absolute top-4 left-4 z-10">
                        <x-ui.badge :variant="$galleryBadge['variant']">{{ $galleryBadge['badge'] }}</x-ui.badge>
                    </div>
                @endif

                <span data-zoom-hint
                      class="pointer-events-none absolute top-4 right-4 z-10 flex items-center gap-1.5 rounded-full bg-white/80 px-3 py-1.5 text-xs font-medium text-navy-700 opacity-0 backdrop-blur-sm transition-opacity duration-300 group-hover/stage:opacity-100">
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5M11 8v6M8 11h6"/>
                    </svg>
                    Hover to zoom
                </span>
            </div>

            {{-- Thumbnails --}}
            @if ($galleryImages->count() > 1)
                <div class="grid {{ $thumbGridClass }} gap-3" role="tablist" aria-label="Product images">
                    @foreach ($galleryImages as $image)
                        <button type="button" data-thumb="{{ $loop->index }}" role="tab"
                                aria-label="View image {{ $loop->iteration }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                class="aspect-square overflow-hidden rounded-xl border-2 transition-all duration-200 {{ $loop->first ? 'border-navy-900 shadow-soft' : 'border-transparent opacity-70 hover:opacity-100' }}">
                            <img src="{{ $image->url() }}"
                                 alt="{{ $image->alt_text ?: $product->name }}"
                                 class="size-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ============ Product info ============ --}}
        <div class="flex flex-col">
            @if ($product->brand)
                <p class="text-sm font-semibold tracking-widest text-bronze-600 uppercase">{{ $product->brand->name }}</p>
            @endif
            <h1 class="mt-2 font-display text-3xl font-bold text-navy-900 sm:text-4xl">{{ $product->name }}</h1>

            @if ($product->short_description)
                <p class="mt-3 text-base leading-relaxed text-navy-600">{{ $product->short_description }}</p>
            @endif

            {{-- Rating --}}
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <x-ui.rating :value="$displayRating" />
                <span class="text-sm font-semibold text-navy-900">{{ $displayRating }}</span>
                <a href="#reviews" class="text-sm text-navy-500 underline-offset-4 transition-colors duration-200 hover:text-navy-900 hover:underline">{{ $displayReviewCount }} {{ Str::plural('review', $displayReviewCount) }}</a>
            </div>

            {{-- Price --}}
            <div class="mt-6 flex flex-wrap items-baseline gap-3">
                <span class="font-display text-3xl font-bold text-navy-900">{{ $product->formattedPrice() }}</span>
                @if ($product->isOnSale())
                    <span class="text-lg text-navy-400 line-through">{{ $product->formattedCompareAtPrice() }}</span>
                    @if ($product->formattedSavings())
                        <x-ui.badge variant="danger">Save {{ $product->formattedSavings() }}</x-ui.badge>
                    @endif
                @endif
            </div>

            {{-- Availability + SKU --}}
            <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
                <span @class([
                    'flex items-center gap-2 font-medium',
                    'text-green-700' => ! $product->isOutOfStock(),
                    'text-red-600' => $product->isOutOfStock(),
                ])>
                    @if (! $product->isOutOfStock())
                        <span class="relative flex size-2.5">
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-green-500 opacity-60"></span>
                            <span class="relative inline-flex size-2.5 rounded-full bg-green-600"></span>
                        </span>
                    @endif
                    {{ $product->detailStockLabel() }}
                </span>
                @if ($product->sku)
                    <span class="text-navy-500">SKU: <span class="font-medium text-navy-700">{{ $product->sku }}</span></span>
                @endif
            </div>

            <hr class="mt-8 border-navy-100">

            {{-- Color picker --}}
            @if ($colors->isNotEmpty())
                <fieldset class="mt-8" data-option-group>
                    <legend class="text-sm font-semibold text-navy-900">
                        Color: <span data-option-label class="font-normal text-navy-600">{{ $colors->first()->value }}</span>
                    </legend>
                    <div class="mt-3 flex flex-wrap gap-3">
                        @foreach ($colors as $color)
                            <button type="button" data-option-value="{{ $color->value }}"
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-label="Color: {{ $color->value }}"
                                    class="flex size-11 items-center justify-center rounded-full ring-2 ring-offset-2 transition-all duration-200 {{ $loop->first ? 'ring-navy-900' : 'ring-transparent hover:ring-navy-300' }}">
                                <span class="size-9 rounded-full {{ $product->colorSwatchClass($color->value) }} shadow-inner"></span>
                            </button>
                        @endforeach
                    </div>
                </fieldset>
            @endif

            {{-- Size picker --}}
            @if ($sizes->isNotEmpty())
                <fieldset class="mt-8" data-option-group>
                    <div class="flex items-center justify-between">
                        <legend class="text-sm font-semibold text-navy-900">
                            Size: <span data-option-label class="font-normal text-navy-600">{{ $sizes->first()->value }}</span>
                        </legend>
                        <a href="#" class="text-sm text-olive-700 underline-offset-4 transition-colors duration-200 hover:text-olive-800 hover:underline">Size guide</a>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2.5">
                        @foreach ($sizes as $size)
                            <button type="button" data-option-value="{{ $size->value }}"
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                    class="min-w-14 rounded-xl border px-4 py-2.5 text-sm font-semibold transition-all duration-200 {{ $loop->first ? 'border-navy-900 bg-navy-900 text-white shadow-soft' : 'border-navy-200 bg-surface text-navy-700 hover:border-navy-400' }}">
                                {{ $size->value }}
                            </button>
                        @endforeach
                    </div>
                </fieldset>
            @endif

            {{-- Quantity + CTAs --}}
            <div class="mt-8" data-atc-anchor>
                <div class="flex flex-wrap items-stretch gap-3">
                    <div class="flex items-center rounded-xl border border-navy-200 bg-surface" data-quantity>
                        <button type="button" data-qty-minus aria-label="Decrease quantity"
                                @disabled($product->isOutOfStock())
                                class="flex size-12 items-center justify-center rounded-l-xl text-navy-600 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-900 disabled:cursor-not-allowed disabled:opacity-40">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>
                        </button>
                        <input type="number" data-qty-input value="1" min="1" max="{{ max(1, $product->stock_quantity) }}" inputmode="numeric" aria-label="Quantity"
                               @disabled($product->isOutOfStock())
                               class="w-12 border-0 bg-transparent text-center text-sm font-semibold text-navy-900 [appearance:textfield] focus:outline-none disabled:opacity-40 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                        <button type="button" data-qty-plus aria-label="Increase quantity"
                                @disabled($product->isOutOfStock())
                                class="flex size-12 items-center justify-center rounded-r-xl text-navy-600 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-900 disabled:cursor-not-allowed disabled:opacity-40">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>

                    <x-ui.button variant="accent" class="flex-1" data-add-to-cart data-product-id="{{ $product->id }}" :disabled="$product->isOutOfStock()">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                        </svg>
                        {{ $product->isOutOfStock() ? 'Out of stock' : 'Add to cart' }}
                    </x-ui.button>
                </div>

                <div class="mt-3 flex flex-wrap items-stretch gap-3">
                    @if ($product->isOutOfStock())
                        <x-ui.button variant="primary" class="flex-1" disabled>Buy it now</x-ui.button>
                    @else
                        <x-ui.button :href="route('checkout')" variant="primary" class="flex-1">Buy it now</x-ui.button>
                    @endif
                    @php($productInWishlist = in_array($product->id, $wishlistProductIds ?? [], true))
                    <x-ui.button variant="outline"
                                 aria-label="{{ $productInWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
                                 data-wishlist-toggle
                                 data-product-id="{{ $product->id }}"
                                 aria-pressed="{{ $productInWishlist ? 'true' : 'false' }}"
                                 @class(['px-4', 'border-bronze-500! bg-bronze-50! text-bronze-700!' => $productInWishlist])>
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 21s-7.5-4.7-9.5-9A5.5 5.5 0 0 1 12 6.5 5.5 5.5 0 0 1 21.5 12c-2 4.3-9.5 9-9.5 9Z"/>
                        </svg>
                    </x-ui.button>
                    @php($productInCompare = in_array($product->id, $compareProductIds ?? [], true))
                    <x-ui.button variant="outline"
                                 aria-label="{{ $productInCompare ? 'Remove from compare' : 'Add to compare' }}"
                                 data-compare-toggle
                                 data-product-id="{{ $product->id }}"
                                 aria-pressed="{{ $productInCompare ? 'true' : 'false' }}"
                                 @class(['px-4', 'border-olive-600! bg-olive-50! text-olive-700!' => $productInCompare])>
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
                    <span><strong class="font-semibold text-navy-900">Free express shipping</strong> on orders over $75. Most items ship within 1–2 business days.</span>
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
                @if ($product->description)
                    <x-ui.accordion-item title="Description" :open="true">
                        @foreach (preg_split('/\R\R+/', trim($product->description)) ?: [] as $paragraph)
                            @if (filled($paragraph))
                                <p @class(['mt-3' => ! $loop->first])>{{ $paragraph }}</p>
                            @endif
                        @endforeach
                    </x-ui.accordion-item>
                @endif

                @if ($product->specifications->isNotEmpty())
                    <x-ui.accordion-item title="Specifications" :open="! $product->description">
                        <dl class="grid grid-cols-1 gap-x-8 gap-y-3 sm:grid-cols-2">
                            @foreach ($product->specifications as $specification)
                                <div class="flex justify-between gap-4 border-b border-navy-50 pb-2">
                                    <dt class="font-medium text-navy-900">{{ $specification->name }}</dt>
                                    <dd class="text-right">{{ $specification->value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </x-ui.accordion-item>
                @endif

                @if ($materials->isNotEmpty())
                    <x-ui.accordion-item title="Materials">
                        <ul class="list-inside list-disc space-y-2">
                            @foreach ($materials as $material)
                                <li>{{ $material->value }}</li>
                            @endforeach
                        </ul>
                    </x-ui.accordion-item>
                @endif

                @if ($careItems->isNotEmpty())
                    <x-ui.accordion-item title="Care Instructions">
                        <ul class="list-inside list-disc space-y-2">
                            @foreach ($careItems as $careItem)
                                <li>{{ $careItem->value }}</li>
                            @endforeach
                        </ul>
                    </x-ui.accordion-item>
                @endif
            </div>
        </div>
    </section>

    {{-- ============ Customer reviews ============ --}}
    <section id="reviews" class="bg-surface py-20" data-reveal>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-ui.section-heading align="left" eyebrow="Customer reviews" title="What the community says" />

            @if (session('success'))
                <div class="mt-6 rounded-card border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-3">
                <div class="rounded-card bg-canvas p-8 lg:self-start">
                    <div class="flex items-baseline gap-3">
                        <span class="font-display text-5xl font-extrabold text-navy-900">{{ $displayRating }}</span>
                        <span class="text-navy-500">out of 5</span>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <x-ui.rating :value="$displayRating" />
                        <span class="text-sm text-navy-500">{{ $displayReviewCount }} {{ Str::plural('review', $displayReviewCount) }}</span>
                    </div>
                    <div class="mt-6 space-y-2.5">
                        @foreach ($reviewSummary['distribution'] as $stars => $sharePercent)
                            <div class="flex items-center gap-3 text-sm">
                                <span class="w-8 shrink-0 font-medium text-navy-700">{{ $stars }} ★</span>
                                <div class="h-2 flex-1 overflow-hidden rounded-full bg-navy-100">
                                    <div class="h-full rounded-full bg-bronze-500" style="width: {{ $sharePercent }}%"></div>
                                </div>
                                <span class="w-10 shrink-0 text-right text-navy-500">{{ $sharePercent }}%</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($customer)
                        @if ($canReview)
                            <div class="mt-8 rounded-card border border-navy-100 bg-surface p-5 shadow-soft">
                                <h3 class="font-display text-lg font-bold text-navy-900">Write a review</h3>
                                <p class="mt-1 text-sm text-navy-500">Share your experience with this verified purchase.</p>
                                <form method="POST" action="{{ route('product.reviews.store', $product) }}" class="mt-5 space-y-5">
                                    @csrf
                                    <input type="hidden" name="redirect" value="product">
                                    <x-account.review-form-fields
                                        rating-picker-id="product-rating"
                                        title-id="product-review-title"
                                        body-id="product-review-body"
                                    />
                                    <x-ui.button variant="secondary" type="submit" class="w-full">Submit review</x-ui.button>
                                </form>
                            </div>
                        @elseif ($hasReviewed)
                            <p class="mt-8 rounded-xl bg-olive-50 px-4 py-3 text-sm text-olive-800">
                                You reviewed this product.
                                <a href="{{ route('account.reviews') }}" class="font-semibold underline-offset-4 hover:underline">Manage your reviews</a>
                            </p>
                        @else
                            <p class="mt-8 rounded-xl bg-navy-50 px-4 py-3 text-sm text-navy-600">
                                Reviews open after your order is delivered.
                                <a href="{{ route('account.orders') }}" class="font-semibold text-navy-900 underline-offset-4 hover:underline">View orders</a>
                            </p>
                        @endif
                    @else
                        <x-ui.button :href="route('login')" variant="outline" class="mt-8 w-full">Sign in to write a review</x-ui.button>
                    @endif
                </div>

                <div class="space-y-6 lg:col-span-2">
                    @forelse ($approvedReviews as $review)
                        <article class="rounded-card bg-canvas p-6 sm:p-8">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-10 items-center justify-center rounded-full bg-navy-900 font-display text-sm font-bold text-bronze-400">
                                        {{ $review->initials() }}
                                    </span>
                                    <div>
                                        <p class="flex items-center gap-2 text-sm font-semibold text-navy-900">
                                            {{ $review->author_name }}
                                            @if ($review->isVerifiedPurchase())
                                                <x-ui.badge variant="success">
                                                    <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                                    Verified purchase
                                                </x-ui.badge>
                                            @endif
                                        </p>
                                        <p class="text-xs text-navy-500">{{ $review->created_at?->format('M j, Y') }}</p>
                                    </div>
                                </div>
                                <x-ui.rating :value="$review->rating" size="sm" />
                            </div>
                            @if ($review->title)
                                <h3 class="mt-4 font-display text-base font-semibold text-navy-900">{{ $review->title }}</h3>
                            @endif
                            <p class="mt-2 text-sm leading-relaxed text-navy-600">{{ $review->body }}</p>
                        </article>
                    @empty
                        <div class="rounded-card bg-canvas p-8 text-center">
                            <p class="text-sm text-navy-600">No reviews yet. Be the first to share your experience after delivery.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- ============ Related products ============ --}}
    @if ($relatedProducts->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8" data-reveal>
            <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                <x-ui.section-heading align="left" eyebrow="You may also like" title="Completes the kit" />
                @if ($product->category)
                    <x-ui.button :href="route('shop', ['category' => $product->category->slug])" variant="outline">View all</x-ui.button>
                @endif
            </div>
            <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                @foreach ($relatedProducts as $relatedProduct)
                    @php($relatedBadge = $relatedProduct->shopBadge())
                    <x-ui.product-card
                        :name="$relatedProduct->name"
                        :brand="$relatedProduct->brand?->name"
                        :category="$relatedProduct->category?->name"
                        :short-description="$relatedProduct->short_description"
                        :price="$relatedProduct->formattedPrice()"
                        :old-price="$relatedProduct->isOnSale() ? $relatedProduct->formattedCompareAtPrice() : null"
                        :badge="$relatedBadge['badge']"
                        :badge-variant="$relatedBadge['variant']"
                        :rating="$relatedProduct->placeholderRating()"
                        :reviews="$relatedProduct->placeholderReviewCount()"
                        :stock="$relatedProduct->shopStockLabel()"
                        :stock-percent="$relatedProduct->shopStockPercent()"
                        :image="$relatedProduct->primaryImageUrl()"
                        :href="route('product.show', $relatedProduct)"
                        :product-id="$relatedProduct->id"
                    />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ Recently viewed ============ --}}
    @if ($recentlyViewedProducts->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-4 sm:px-6 lg:px-8" data-reveal>
            <x-ui.section-heading align="left" eyebrow="Pick up where you left off" title="Recently viewed" />
            <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                @foreach ($recentlyViewedProducts as $viewedProduct)
                    <x-ui.product-card
                        :name="$viewedProduct->name"
                        :brand="$viewedProduct->brand?->name"
                        :category="$viewedProduct->category?->name"
                        :price="$viewedProduct->formattedPrice()"
                        :old-price="$viewedProduct->isOnSale() ? $viewedProduct->formattedCompareAtPrice() : null"
                        :image="$viewedProduct->primaryImageUrl()"
                        :href="route('product.show', $viewedProduct)"
                        :product-id="$viewedProduct->id"
                    />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ Sticky add-to-cart bar ============ --}}
    <div data-sticky-atc
         class="glass fixed inset-x-0 bottom-0 z-40 translate-y-full border-t border-navy-900/5 transition-transform duration-300 ease-out"
         aria-hidden="true">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
                <span class="hidden size-12 shrink-0 overflow-hidden rounded-xl bg-navy-100 sm:flex">
                    @if ($primaryImageUrl)
                        <img src="{{ $primaryImageUrl }}" alt="" class="size-full object-cover">
                    @else
                        <span class="flex size-full items-center justify-center bg-linear-to-br from-navy-200 via-navy-100 to-bronze-100">
                            <svg class="size-6 text-navy-400/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z"/>
                            </svg>
                        </span>
                    @endif
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
            <x-ui.button variant="accent" size="sm" class="shrink-0" data-add-to-cart data-product-id="{{ $product->id }}" :disabled="$product->isOutOfStock()">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                </svg>
                {{ $product->isOutOfStock() ? 'Out of stock' : 'Add to cart' }}
            </x-ui.button>
        </div>
    </div>
</x-layouts.app>
