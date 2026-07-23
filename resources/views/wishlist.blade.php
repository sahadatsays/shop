@php
    /** @var \App\DTOs\Wishlist\WishlistSummary $summary */
    $availabilityMeta = [
        'in-stock' => ['label' => 'In stock', 'dot' => 'bg-green-500', 'text' => 'text-green-700'],
        'low-stock' => ['label' => 'Low stock — order soon', 'dot' => 'bg-bronze-500', 'text' => 'text-bronze-700'],
        'out-of-stock' => ['label' => 'Out of stock', 'dot' => 'bg-red-500', 'text' => 'text-red-600'],
    ];
@endphp

<x-layouts.app title="Wishlist" description="The gear you've saved for later at Valor Supply Co. — keep an eye on availability and move favorites to your cart.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-wishlist>

        {{-- Header --}}
        <nav aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                <li><a href="{{ route('home') }}" class="transition-colors duration-200 hover:text-navy-900">Home</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="font-medium text-navy-900">Wishlist</li>
            </ol>
        </nav>

        <div class="mt-4 flex flex-wrap items-end justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-baseline gap-3">
                    <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">Your wishlist</h1>
                    <p class="text-navy-500" data-wishlist-count-label>{{ $summary->itemCount }} saved {{ $summary->itemCount === 1 ? 'item' : 'items' }}</p>
                </div>
                <p class="mt-2 max-w-xl text-navy-600">Saved gear stays here so you never lose sight of it. We'll flag anything running low.</p>
            </div>
            <div class="flex flex-wrap gap-3" data-wishlist-actions @if($summary->isEmpty()) hidden @endif>
                <x-ui.button variant="outline" size="sm" data-wishlist-clear>
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M6 7l1 13a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-13"/>
                    </svg>
                    Clear all
                </x-ui.button>
                <x-ui.button variant="secondary" size="sm" data-wishlist-add-all>
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                    </svg>
                    Add all to cart
                </x-ui.button>
            </div>
        </div>

        {{-- Grid --}}
        <ul data-wishlist-grid class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" @if($summary->isEmpty()) hidden @endif>
            @foreach ($summary->items as $line)
                @php
                    $product = $line->product;
                    $availability = $line->availability();
                    $meta = $availabilityMeta[$availability];
                @endphp
                <li data-wishlist-item
                    data-wishlist-item-id="{{ $line->wishlistItem->id }}"
                    data-product-id="{{ $product->id }}"
                    data-availability="{{ $availability }}"
                    class="group flex flex-col overflow-hidden rounded-card bg-surface shadow-soft transition-all duration-300 hover:-translate-y-1 hover:shadow-card-hover">

                    <div class="relative aspect-4/3 overflow-hidden bg-navy-100">
                        <a href="{{ route('product.show', $product) }}" class="block size-full">
                            @if ($product->primaryImageUrl())
                                <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}" loading="lazy"
                                     class="size-full object-cover transition-transform duration-500 group-hover:scale-105 {{ $availability === 'out-of-stock' ? 'opacity-60 saturate-50' : '' }}">
                            @else
                                <div class="flex size-full items-center justify-center bg-linear-to-br from-navy-100 via-navy-50 to-bronze-100">
                                    <svg class="size-14 text-navy-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="9" cy="9" r="2"/><path d="m21 15-4.5-4.5L7 20"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        @if ($product->isOnSale())
                            <x-ui.badge variant="danger" class="absolute top-4 left-4">Sale</x-ui.badge>
                        @elseif ($availability === 'out-of-stock')
                            <x-ui.badge variant="navy" class="absolute top-4 left-4">Out of stock</x-ui.badge>
                        @endif

                        <button type="button"
                                data-wishlist-remove
                                data-wishlist-item-id="{{ $line->wishlistItem->id }}"
                                aria-label="Remove {{ $product->name }} from wishlist"
                                class="absolute top-4 right-4 flex size-10 items-center justify-center rounded-full bg-white/90 text-navy-600 shadow-soft backdrop-blur-sm transition-colors duration-200 hover:bg-red-600 hover:text-white">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                                <path d="m6 6 12 12M18 6 6 18"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-1 flex-col p-6">
                        @if ($product->category)
                            <p class="text-xs font-semibold tracking-wide text-bronze-600 uppercase">{{ $product->category->name }}</p>
                        @endif
                        <h2 class="mt-1.5 font-display text-lg font-bold text-navy-900">
                            <a href="{{ route('product.show', $product) }}" class="transition-colors duration-200 hover:text-olive-700">{{ $product->name }}</a>
                        </h2>

                        <div class="mt-2 flex items-center gap-2">
                            <x-ui.rating :value="$product->placeholderRating()" size="sm" />
                            <span class="text-xs text-navy-500">{{ $product->placeholderRating() }} ({{ $product->placeholderReviewCount() }})</span>
                        </div>

                        <p class="mt-3 flex items-baseline gap-2">
                            <span class="font-display text-xl font-extrabold text-navy-900">{{ $product->formattedPrice() }}</span>
                            @if ($product->isOnSale())
                                <span class="text-sm text-navy-400 line-through">{{ $product->formattedCompareAtPrice() }}</span>
                            @endif
                        </p>

                        <p class="mt-2 flex items-center gap-2 text-sm font-medium {{ $meta['text'] }}">
                            <span class="relative flex size-2" aria-hidden="true">
                                @if ($availability !== 'out-of-stock')
                                    <span class="absolute inline-flex size-full animate-ping rounded-full {{ $meta['dot'] }} opacity-60"></span>
                                @endif
                                <span class="relative inline-flex size-2 rounded-full {{ $meta['dot'] }}"></span>
                            </span>
                            {{ $meta['label'] }}
                        </p>

                        <div class="mt-5 flex gap-2 pt-1">
                            @if ($availability === 'out-of-stock')
                                <x-ui.button variant="outline" class="flex-1" data-notify-me>
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 9a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6"/><path d="M10 19a2 2 0 0 0 4 0"/>
                                    </svg>
                                    <span data-action-label>Notify me</span>
                                </x-ui.button>
                            @else
                                <x-ui.button class="flex-1"
                                             data-wishlist-move-to-cart
                                             data-wishlist-item-id="{{ $line->wishlistItem->id }}"
                                             data-product-id="{{ $product->id }}">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                                    </svg>
                                    <span data-action-label>Move to cart</span>
                                </x-ui.button>
                            @endif
                            @php($wishlistInCompare = in_array($product->id, $compareProductIds ?? [], true))
                            <button type="button"
                                    data-compare-toggle
                                    data-product-id="{{ $product->id }}"
                                    aria-pressed="{{ $wishlistInCompare ? 'true' : 'false' }}"
                                    aria-label="{{ $wishlistInCompare ? 'Remove' : 'Add' }} {{ $product->name }} {{ $wishlistInCompare ? 'from' : 'to' }} compare"
                                    @class([
                                        'flex size-11.5 shrink-0 items-center justify-center rounded-xl border border-navy-200 text-navy-600 transition-colors duration-200 hover:border-navy-300 hover:bg-navy-50 hover:text-navy-900',
                                        'border-olive-600 bg-olive-50 text-olive-700' => $wishlistInCompare,
                                    ])>
                                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M8 3 4 7l4 4M4 7h16M16 21l4-4-4-4M20 17H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Empty state --}}
        <div data-wishlist-empty @class(['mx-auto max-w-md py-20 text-center', 'hidden' => ! $summary->isEmpty()])>
            <svg class="mx-auto h-44 w-auto" viewBox="0 0 220 160" fill="none" aria-hidden="true">
                <circle cx="110" cy="78" r="58" class="fill-navy-100"/>
                <path d="M110 112s-30-19-38-36a22 22 0 0 1 38-22 22 22 0 0 1 38 22c-8 17-38 36-38 36Z"
                      class="fill-white stroke-navy-900" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M95 62c2-6 8-9 14-8" class="stroke-navy-300" stroke-width="3" stroke-linecap="round"/>
                <path d="M162 38c3.6 0 6.5-2.9 6.5-6.5 0 3.6 2.9 6.5 6.5 6.5-3.6 0-6.5 2.9-6.5 6.5 0-3.6-2.9-6.5-6.5-6.5Z" class="fill-bronze-400"/>
                <path d="M48 116c2.8 0 5-2.2 5-5 0 2.8 2.2 5 5 5-2.8 0-5 2.2-5 5 0-2.8-2.2-5-5-5Z" class="fill-olive-300"/>
                <circle cx="168" cy="112" r="3.5" class="fill-navy-300"/>
            </svg>
            <h2 class="mt-6 font-display text-2xl font-bold text-navy-900">Your wishlist is empty</h2>
            <p class="mt-3 leading-relaxed text-navy-600">
                Tap the heart on any product to save it here. Your favorites will be waiting whenever you're ready.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <x-ui.button :href="route('shop')" variant="secondary">Explore the shop</x-ui.button>
                <x-ui.button :href="route('categories')" variant="outline">Browse categories</x-ui.button>
            </div>
        </div>
    </div>
</x-layouts.app>
