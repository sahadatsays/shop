@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Review> $reviews */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Product> $reviewableProducts */
    $filters = ['All', '5 stars', '4 stars', '3 stars & below'];
@endphp

<x-layouts.app title="My Reviews" description="Manage your Valor Supply Co. product reviews — edit and delete feedback from verified purchases.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-reviews @if($openWriteProductId) data-open-write-product-id="{{ $openWriteProductId }}" @endif>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">

            <x-account.sidebar active="Reviews" />

            <div class="lg:col-span-3">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                        <li><a href="{{ route('account') }}" class="transition-colors duration-200 hover:text-navy-900">Account</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="font-medium text-navy-900">Reviews</li>
                    </ol>
                </nav>

                <div class="mt-4 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">My reviews</h1>
                        <p class="mt-2 text-navy-600">Share feedback on delivered orders — one review per product.</p>
                    </div>
                    <x-ui.button variant="outline" size="sm" :href="route('shop')" class="shrink-0">
                        Browse products
                    </x-ui.button>
                </div>

                @if (session('success'))
                    <div class="mt-6 rounded-card border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($reviewableProducts->isNotEmpty())
                    <div id="ready-for-review" class="mt-8 rounded-card border border-bronze-200 bg-bronze-50/60 p-6 shadow-soft">
                        <h2 class="font-display text-lg font-bold text-navy-900">Ready for your review</h2>
                        <p class="mt-1 text-sm text-navy-600">These products were delivered and are waiting for feedback.</p>
                        <ul class="mt-4 space-y-3">
                            @foreach ($reviewableProducts as $product)
                                <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-surface px-4 py-3 shadow-soft">
                                    <div class="flex min-w-0 items-center gap-3">
                                        @if ($product->primaryImageUrl())
                                            <img src="{{ $product->primaryImageUrl() }}" alt="" class="size-14 shrink-0 rounded-xl object-cover ring-1 ring-navy-900/5">
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-medium text-navy-900">{{ $product->name }}</p>
                                            @if ($product->category)
                                                <p class="text-xs text-navy-500">{{ $product->category->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button"
                                            data-review-write
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->name }}"
                                            data-store-url="{{ route('product.reviews.store', $product) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-olive-600 px-4 py-2 text-sm font-semibold text-white shadow-soft transition-all duration-200 hover:bg-olive-700 hover:shadow-card active:scale-[0.98]">
                                        Write review
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-card bg-surface p-5 shadow-soft">
                        <p class="text-sm font-medium text-navy-500">Total reviews</p>
                        <p class="mt-1 font-display text-2xl font-bold text-navy-900" data-reviews-total>{{ $stats['total'] }}</p>
                    </div>
                    <div class="rounded-card bg-surface p-5 shadow-soft">
                        <p class="text-sm font-medium text-navy-500">Average rating</p>
                        <div class="mt-1 flex items-center gap-2">
                            <p class="font-display text-2xl font-bold text-navy-900" data-reviews-avg>{{ $stats['average'] ?? '—' }}</p>
                            @if ($stats['average'])
                                <x-ui.rating :value="$stats['average']" size="sm" data-reviews-avg-stars />
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap gap-2" role="group" aria-label="Filter reviews by rating">
                        @foreach ($filters as $filter)
                            <button type="button" data-reviews-filter="{{ $filter }}"
                                    aria-pressed="{{ $filter === 'All' ? 'true' : 'false' }}"
                                    class="rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 {{ $filter === 'All' ? 'bg-navy-900 text-white shadow-soft' : 'bg-surface text-navy-700 shadow-soft hover:bg-navy-900/5' }}">
                                {{ $filter }}
                            </button>
                        @endforeach
                    </div>
                    <label class="flex items-center gap-2 text-sm text-navy-600">
                        <span class="sr-only">Sort reviews</span>
                        <select data-reviews-sort class="rounded-field border border-navy-200 bg-surface px-3 py-2 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                            <option value="newest">Newest first</option>
                            <option value="oldest">Oldest first</option>
                            <option value="rating-high">Highest rating</option>
                            <option value="rating-low">Lowest rating</option>
                        </select>
                    </label>
                </div>

                <p class="mt-4 text-sm text-navy-500" data-reviews-count aria-live="polite">{{ $stats['total'] }} {{ Str::plural('review', $stats['total']) }}</p>

                <div class="mt-4 space-y-5" data-reviews-list @if($reviews->isEmpty()) hidden @endif>
                    @foreach ($reviews as $review)
                        @php($product = $review->product)
                        <article
                            data-review-card
                            data-review-id="{{ $review->id }}"
                            data-update-url="{{ route('account.reviews.update', $review) }}"
                            data-delete-url="{{ route('account.reviews.destroy', $review) }}"
                            data-rating="{{ $review->rating }}"
                            data-date="{{ $review->created_at?->format('Y-m-d') }}"
                            data-title="{{ $review->title ?? '' }}"
                            data-body="{{ $review->body }}"
                            data-product="{{ $product?->name ?? 'Product' }}"
                            class="overflow-hidden rounded-card bg-surface shadow-soft transition-all duration-300 ease-out hover:shadow-card"
                        >
                            <div class="flex flex-col sm:flex-row">
                                @if ($product)
                                    <a href="{{ route('product.show', $product) }}" class="group relative shrink-0 sm:w-44 lg:w-52">
                                        <div class="aspect-4/3 overflow-hidden bg-navy-50 sm:aspect-auto sm:h-full sm:min-h-44">
                                            @if ($product->primaryImageUrl())
                                                <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}"
                                                     class="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                     loading="lazy">
                                            @endif
                                        </div>
                                    </a>
                                @endif

                                <div class="flex flex-1 flex-col p-6 sm:p-7">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            @if ($product)
                                                <a href="{{ route('product.show', $product) }}" class="font-display text-lg font-bold text-navy-900 transition-colors duration-200 hover:text-bronze-600">{{ $product->name }}</a>
                                            @endif
                                            <p class="mt-0.5 text-sm text-navy-500">
                                                @if ($product?->category)
                                                    {{ $product->category->name }} ·
                                                @endif
                                                Order #{{ $review->order?->order_number }}
                                            </p>
                                        </div>
                                        <div data-review-rating-display>
                                            <x-ui.rating :value="$review->rating" size="sm" />
                                        </div>
                                    </div>

                                    @if ($review->title)
                                        <h2 class="mt-4 font-display text-base font-semibold text-navy-900" data-review-title>{{ $review->title }}</h2>
                                    @endif
                                    <p class="mt-2 text-sm leading-relaxed text-navy-600" data-review-body>{{ $review->body }}</p>

                                    <div class="mt-auto flex flex-wrap items-center justify-between gap-4 pt-6">
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-navy-500">
                                            <time datetime="{{ $review->created_at?->toDateString() }}" data-review-date>{{ $review->created_at?->format('M j, Y') }}</time>
                                            @if ($review->isVerifiedPurchase())
                                                <x-ui.badge variant="success">
                                                    <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                                    Verified purchase
                                                </x-ui.badge>
                                            @endif
                                        </div>

                                        <div class="flex gap-2">
                                            <button type="button" data-review-edit aria-label="Edit review for {{ $product?->name }}"
                                                    class="inline-flex items-center gap-1.5 rounded-xl border border-navy-200 bg-surface px-3 py-2 text-xs font-semibold text-navy-900 transition-colors duration-200 hover:border-navy-300 hover:bg-navy-50">
                                                Edit
                                            </button>
                                            <button type="button" data-review-delete aria-label="Delete review for {{ $product?->name }}"
                                                    class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-surface px-3 py-2 text-xs font-semibold text-red-600 transition-colors duration-200 hover:border-red-300 hover:bg-red-50">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div data-reviews-empty @class(['mt-8 rounded-card border border-navy-100 bg-surface p-12 text-center shadow-soft', 'hidden' => $reviews->isNotEmpty()])>
                    <h2 class="font-display text-xl font-bold text-navy-900">No reviews yet</h2>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-navy-600">After your order is delivered, come back here to share your experience.</p>
                    <x-ui.button variant="primary" size="sm" :href="route('account.orders')" class="mt-6">View orders</x-ui.button>
                </div>

                <div data-reviews-filter-empty hidden class="mt-8 rounded-card border border-navy-100 bg-surface p-10 text-center shadow-soft">
                    <p class="text-sm text-navy-600">No reviews match this filter.</p>
                    <button type="button" data-reviews-clear-filter class="mt-4 text-sm font-semibold text-bronze-600 underline-offset-4 hover:underline">Show all reviews</button>
                </div>

                <p class="mt-8 text-sm text-navy-500" data-reviews-status aria-live="polite"></p>
            </div>
        </div>

        <dialog data-review-write-dialog class="fixed inset-0 m-auto h-fit max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-card border-0 bg-surface p-0 shadow-glass backdrop:bg-navy-900/40 open:animate-scale-in">
            <form method="POST" action="" class="p-7" data-review-write-form novalidate>
                @csrf
                <input type="hidden" name="redirect" value="account">
                <input type="hidden" name="write_product_id" value="" data-write-product-id>

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-display text-xl font-bold text-navy-900">Write a review</h2>
                        <p class="mt-1 text-sm text-navy-500" data-write-product-name>Product</p>
                    </div>
                    <button type="button" data-review-write-close aria-label="Close dialog"
                            class="flex size-9 shrink-0 items-center justify-center rounded-xl text-navy-500 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-6 space-y-5">
                    <x-account.review-form-fields
                        rating-picker-id="write-rating"
                        title-id="write-review-title"
                        body-id="write-review-body"
                    />
                </div>

                <div class="mt-8 flex flex-wrap justify-end gap-3">
                    <x-ui.button variant="outline" size="sm" type="button" data-review-write-close>Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit">Publish review</x-ui.button>
                </div>
            </form>
        </dialog>

        <dialog data-review-dialog class="fixed inset-0 m-auto h-fit max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-card border-0 bg-surface p-0 shadow-glass backdrop:bg-navy-900/40 open:animate-scale-in">
            <form method="POST" class="p-7" data-review-form novalidate>
                @csrf
                @method('PATCH')
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-display text-xl font-bold text-navy-900">Edit review</h2>
                        <p class="mt-1 text-sm text-navy-500" data-edit-product-name>Product</p>
                    </div>
                    <button type="button" data-review-dialog-close aria-label="Close dialog"
                            class="flex size-9 shrink-0 items-center justify-center rounded-xl text-navy-500 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-6 space-y-5">
                    <x-account.review-form-fields
                        rating-picker-id="edit-rating"
                        title-id="review-title"
                        body-id="review-body"
                    />
                </div>

                <div class="mt-8 flex flex-wrap justify-end gap-3">
                    <x-ui.button variant="outline" size="sm" type="button" data-review-dialog-close>Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit">Save review</x-ui.button>
                </div>
            </form>
        </dialog>

        <dialog data-review-delete-dialog class="fixed inset-0 m-auto h-fit w-full max-w-md rounded-card border-0 bg-surface p-0 shadow-glass backdrop:bg-navy-900/40 open:animate-scale-in">
            <form method="POST" data-review-delete-form>
                @csrf
                @method('DELETE')
                <div class="p-7">
                    <h2 class="font-display text-xl font-bold text-navy-900">Delete this review?</h2>
                    <p class="mt-2 text-sm text-navy-600">This permanently removes your review for <strong data-delete-product>this product</strong>.</p>
                    <div class="mt-8 flex flex-wrap justify-end gap-3">
                        <x-ui.button variant="outline" size="sm" type="button" data-delete-cancel>Keep review</x-ui.button>
                        <x-ui.button variant="primary" size="sm" type="submit" class="!bg-red-600 hover:!bg-red-700">Delete review</x-ui.button>
                    </div>
                </div>
            </form>
        </dialog>
    </div>

</x-layouts.app>
