@php
    /** @var \App\DTOs\Cart\CartSummary $summary */
    $freeShippingThreshold = config('cart.free_shipping_threshold_cents', 7500) / 100;
    $taxRate = config('cart.tax_rate', 0.08);
    $flatShipping = config('cart.flat_shipping_cents', 900) / 100;
@endphp

<x-layouts.app title="Shopping Cart"
    description="Review your Jackpot BD LTD order — free express shipping over {{ \App\Support\MoneyFormatter::format((int) config('cart.free_shipping_threshold_cents', 7500)) }} ">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-cart
        data-free-shipping-threshold="{{ $freeShippingThreshold }}" data-tax-rate="{{ $taxRate }}"
        data-flat-shipping="{{ $flatShipping }}" data-discount-cents="{{ $summary->discountCents }}">

        {{-- Header --}}
        <nav aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                <li><a href="{{ route('home') }}" class="transition-colors duration-200 hover:text-navy-900">Home</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="font-medium text-navy-900">Cart</li>
            </ol>
        </nav>
        <div class="mt-4 flex flex-wrap items-baseline gap-3">
            <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">Your cart</h1>
            <p class="text-navy-500" data-cart-count-label>{{ $summary->itemCount }}
                {{ $summary->itemCount === 1 ? 'item' : 'items' }}</p>
        </div>

        @if (session('cart_warning'))
            <div class="mt-6 rounded-card border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
                {{ session('cart_warning') }}
            </div>
        @endif

        @if ($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->has('cart'))
            <div class="mt-6 rounded-card border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
                {{ $errors->first('cart') }}
            </div>
        @elseif (is_string($cartError = session('errors')?->first('cart') ?? null))
            <div class="mt-6 rounded-card border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
                {{ $cartError }}
            </div>
        @endif

        {{-- Free shipping progress --}}
        {{-- <div class="mt-6 rounded-card bg-olive-50 p-5 @if ($summary->isEmpty()) hidden @endif"
            data-shipping-bar>
            <p class="text-sm text-navy-700" data-shipping-message></p>
            <div class="mt-3 h-2 overflow-hidden rounded-full bg-olive-100">
                <div data-shipping-progress class="h-full rounded-full bg-olive-600 transition-all duration-500"
                    style="width: 0%"></div>
            </div>
        </div> --}}

        <div class="mt-8 grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ Cart items ============ --}}
            <div class="lg:col-span-2">
                <div
                    class="hidden grid-cols-12 gap-4 border-b border-navy-100 pb-3 text-xs font-semibold tracking-wide text-navy-500 uppercase sm:grid @if ($summary->isEmpty()) hidden @endif">
                    <span class="col-span-6">Product</span>
                    <span class="col-span-3 text-center">Quantity</span>
                    <span class="col-span-2 text-right">Total</span>
                    <span class="col-span-1"></span>
                </div>

                <ul data-cart-items class="divide-y divide-navy-100">
                    @foreach ($summary->items as $line)
                        @php
                            $item = $line->cartItem;
                            $product = $line->product;
                            $unitPrice = $item->unit_price_cents / 100;
                        @endphp
                        <li data-cart-item data-cart-item-id="{{ $item->id }}" data-price="{{ $unitPrice }}"
                            class="grid grid-cols-1 gap-4 py-6 transition-opacity duration-300 sm:grid-cols-12 sm:items-center">

                            {{-- Product --}}
                            <div class="flex items-center gap-4 sm:col-span-6">
                                <a href="{{ $line->productUrl() }}"
                                    class="block size-20 shrink-0 overflow-hidden rounded-xl bg-navy-100 sm:size-24">
                                    @if ($line->imageUrl())
                                        <img src="{{ $line->imageUrl() }}" alt="{{ $product->name }}" loading="lazy"
                                            class="size-full object-cover">
                                    @else
                                        <div
                                            class="flex size-full items-center justify-center bg-linear-to-br from-navy-100 via-navy-50 to-bronze-100">
                                            <svg class="size-8 text-navy-300" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                                <rect x="3" y="3" width="18" height="18" rx="3" />
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                                <div class="min-w-0">
                                    <a href="{{ $line->productUrl() }}"
                                        class="block truncate font-display text-base font-semibold text-navy-900 transition-colors duration-200 hover:text-olive-700">
                                        {{ $product->name }}
                                    </a>
                                    @if ($product->category)
                                        <p class="mt-0.5 text-sm text-navy-500">{{ $product->category->name }}</p>
                                    @endif
                                    <p class="mt-1.5 flex items-baseline gap-2 text-sm">
                                        <span
                                            class="font-semibold text-navy-900">{{ $line->formattedUnitPrice() }}</span>
                                        @if ($product->isLowStock())
                                            <x-ui.badge variant="warning">Only {{ $product->stock_quantity }}
                                                left</x-ui.badge>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Quantity --}}
                            <div class="sm:col-span-3 sm:justify-self-center">
                                <div class="flex w-fit items-center rounded-xl border border-navy-200 bg-surface"
                                    data-quantity>
                                    <button type="button" data-qty-minus
                                        aria-label="Decrease quantity of {{ $product->name }}"
                                        class="flex size-10 items-center justify-center rounded-l-xl text-navy-600 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-900">
                                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                            <path d="M5 12h14" />
                                        </svg>
                                    </button>
                                    <input type="number" data-qty-input value="{{ $item->quantity }}" min="1"
                                        max="{{ config('cart.max_quantity_per_item', 99) }}" inputmode="numeric"
                                        aria-label="Quantity of {{ $product->name }}"
                                        class="w-10 border-0 bg-transparent text-center text-sm font-semibold text-navy-900 [appearance:textfield] focus:outline-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                                    <button type="button" data-qty-plus
                                        aria-label="Increase quantity of {{ $product->name }}"
                                        class="flex size-10 items-center justify-center rounded-r-xl text-navy-600 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-900">
                                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                            <path d="M12 5v14M5 12h14" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Line total --}}
                            <p class="text-base font-bold text-navy-900 tabular-nums sm:col-span-2 sm:text-right"
                                data-line-total>
                                {{ $line->formattedLineTotal() }}
                            </p>

                            {{-- Remove --}}
                            <div class="sm:col-span-1 sm:justify-self-end">
                                <button type="button" data-cart-remove
                                    aria-label="Remove {{ $product->name }} from cart"
                                    class="flex size-10 items-center justify-center rounded-xl text-navy-400 transition-colors duration-200 hover:bg-red-50 hover:text-red-600">
                                    <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <path
                                            d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M6 7l1 13a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-13M10 11v6M14 11v6" />
                                    </svg>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- Empty state --}}
                <div data-cart-empty @if (!$summary->isEmpty()) hidden @endif class="py-16 text-center">
                    <svg class="mx-auto h-36 w-auto" viewBox="0 0 200 150" fill="none" aria-hidden="true">
                        <circle cx="100" cy="72" r="54" class="fill-navy-100" />
                        <path d="M62 52h14l10 46a6 6 0 0 0 6 5h34a6 6 0 0 0 6-5l8-34H82" class="stroke-navy-900"
                            stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="white" />
                        <circle cx="96" cy="118" r="6" class="fill-navy-900" />
                        <circle cx="122" cy="118" r="6" class="fill-navy-900" />
                        <path d="M104 76l12 12m0-12-12 12" class="stroke-bronze-500" stroke-width="3"
                            stroke-linecap="round" />
                        <path d="M148 34c3.3 0 6-2.7 6-6 0 3.3 2.7 6 6 6-3.3 0-6 2.7-6 6 0-3.3-2.7-6-6-6Z"
                            class="fill-olive-300" />
                    </svg>
                    <h2 class="mt-6 font-display text-xl font-bold text-navy-900">Your cart is empty</h2>
                    <p class="mt-2 text-navy-600">Looks like you haven't added anything yet. The gear is waiting.</p>
                    <x-ui.button :href="route('shop')" variant="secondary" class="mt-6">Continue shopping</x-ui.button>
                </div>

                @if (auth('customer')->check())
                    <div class="mt-8 flex justify-end">
                        <button type="button" data-save-cart
                            class="text-sm font-medium text-olive-700 underline-offset-4 hover:underline">
                            Save cart for later
                        </button>
                    </div>
                @endif

                {{-- Coupon --}}
                <div data-coupon-section @if ($summary->isEmpty()) hidden @endif
                    class="mt-8 rounded-card bg-surface p-6 shadow-soft">
                    <h2 class="flex items-center gap-2 font-display text-base font-semibold text-navy-900">
                        <svg class="size-5 text-bronze-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path
                                d="M4 9V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a3 3 0 0 0 0 6v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2a3 3 0 0 0 0-6Z" />
                            <path d="M14 5v2m0 4v2m0 4v2" />
                        </svg>
                        Have a coupon?
                    </h2>
                    <form data-coupon-form class="mt-4 flex flex-col gap-3 sm:flex-row" @if ($summary->hasDiscount()) hidden @endif>
                        <label for="coupon-code" class="sr-only">Coupon code</label>
                        <input type="text" id="coupon-code" data-coupon-input
                            placeholder="Enter code"
                            class="w-full rounded-xl border border-navy-200 bg-canvas px-4 py-3 text-sm text-ink uppercase placeholder:normal-case placeholder:text-navy-400 transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                        <x-ui.button type="submit" variant="outline" class="shrink-0">Apply coupon</x-ui.button>
                    </form>
                    <p data-coupon-error hidden class="mt-3 text-sm font-medium text-red-600"></p>
                    <div data-coupon-applied @if (! $summary->hasDiscount()) hidden @endif
                        class="mt-4 flex items-center justify-between rounded-xl bg-green-50 px-4 py-3">
                        <p class="flex items-center gap-2 text-sm font-semibold text-green-800">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m5 13 4 4L19 7" />
                            </svg>
                            <span data-coupon-applied-label>{{ $summary->discountLabel() }}</span>
                        </p>
                        <button type="button" data-coupon-remove aria-label="Remove coupon"
                            class="text-sm font-medium text-green-700 underline-offset-4 hover:underline">Remove</button>
                    </div>
                </div>
            </div>

            {{-- ============ Order summary ============ --}}
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-card bg-surface p-7 shadow-card">
                    <h2 class="font-display text-lg font-bold text-navy-900">Order summary</h2>

                    <dl class="mt-6 space-y-4 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-navy-600">Subtotal (<span
                                    data-summary-count>{{ $summary->itemCount }}</span> items)</dt>
                            <dd class="font-semibold text-navy-900 tabular-nums" data-summary-subtotal>
                                {{ $summary->formattedSubtotal() }}</dd>
                        </div>
                        <div class="flex items-center justify-between" data-summary-discount-row @if (! $summary->hasDiscount()) hidden @endif>
                            <dt class="text-navy-600" data-summary-discount-label>Coupon{{ $summary->discount?->code ? ' ('.$summary->discount->code.')' : '' }}</dt>
                            <dd class="font-semibold text-green-700 tabular-nums" data-summary-discount>
                                {{ $summary->hasDiscount() ? $summary->formattedDiscount() : '' }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-navy-600">Shipping</dt>
                            <dd class="font-semibold text-navy-900 tabular-nums" data-summary-shipping>
                                {{ $summary->formattedShipping() }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-navy-600">Estimated tax ({{ number_format($taxRate * 100, 0) }}%)</dt>
                            <dd class="font-semibold text-navy-900 tabular-nums" data-summary-tax>
                                {{ $summary->formattedTax() }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-navy-100 pt-4 text-base">
                            <dt class="font-display font-bold text-navy-900">Total</dt>
                            <dd class="font-display text-xl font-extrabold text-navy-900 tabular-nums"
                                data-summary-total>{{ $summary->formattedTotal() }}</dd>
                        </div>
                    </dl>

                    @if ($summary->isEmpty())
                        <x-ui.button :href="route('shop')" variant="accent" class="mt-7 w-full" size="lg">Continue
                            shopping</x-ui.button>
                    @else
                        <x-ui.button :href="route('checkout')" variant="accent" class="mt-7 w-full" size="lg">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <rect x="4" y="10" width="16" height="10" rx="2" />
                                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                            </svg>
                            Proceed to checkout
                        </x-ui.button>
                        <x-ui.button :href="route('shop')" variant="ghost" class="mt-3 w-full">Continue
                            shopping</x-ui.button>
                    @endif

                    <ul class="mt-6 flex flex-wrap justify-center gap-2 border-t border-navy-100 pt-6">
                        @foreach (['VISA', 'Mastercard', 'AMEX', 'PayPal', 'Apple Pay'] as $method)
                            <li
                                class="rounded-lg border border-navy-200 px-2.5 py-1 text-[0.65rem] font-bold tracking-wide text-navy-600">
                                {{ $method }}</li>
                        @endforeach
                    </ul>
                </div>

                {{-- Trust badges --}}
                {{-- <ul class="mt-6 space-y-3 rounded-card bg-navy-900 p-6 text-sm text-navy-200">
                    @foreach ([['label' => '256-bit encrypted secure checkout', 'icon' => 'M4 10h16v10H4zM8 10V7a4 4 0 0 1 8 0v3'], ['label' => '30-day hassle-free returns', 'icon' => 'M3 12a9 9 0 1 0 3-6.7M3 4v4h4'], ['label' => 'Lifetime craftsmanship warranty', 'icon' => 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z'], ['label' => '5% of profits support veterans', 'icon' => 'M12 21s-7.5-4.7-9.5-9A5.5 5.5 0 0 1 12 6.5 5.5 5.5 0 0 1 21.5 12c-2 4.3-9.5 9-9.5 9Z']] as $badge)
                        <li class="flex items-center gap-3">
                            <svg class="size-4.5 shrink-0 text-bronze-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="{{ $badge['icon'] }}" />
                            </svg>
                            {{ $badge['label'] }}
                        </li>
                    @endforeach
                </ul> --}}
            </aside>
        </div>

        {{-- ============ Recommended products ============ --}}
        @if ($recommended->isNotEmpty())
            <section class="mt-20" data-reveal>
                <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                    <x-ui.section-heading align="left" eyebrow="You may also like" title="Complete your loadout" />
                    <x-ui.button :href="route('shop')" variant="outline">Browse the shop</x-ui.button>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($recommended as $product)
                        <x-ui.product-card :name="$product->name" :category="$product->category?->name" :price="$product->formattedPrice()" :badge="$product->is_new_arrival ? 'New' : ($product->is_featured ? 'Featured' : null)"
                            :badge-variant="$product->is_new_arrival ? 'olive' : 'bronze'" :image="$product->primaryImageUrl()" :href="route('product.show', $product)" :product-id="$product->id" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
