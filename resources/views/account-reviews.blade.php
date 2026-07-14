@php
    $reviews = [
        [
            'id' => 'review-1',
            'product' => 'Ranger Field Jacket',
            'category' => 'Apparel',
            'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&q=70&auto=format&fit=crop',
            'href' => route('product.show'),
            'order' => '#VS-10482',
            'rating' => 5,
            'title' => 'Built like it was issued — only better',
            'body' => 'This jacket has been through two Midwest winters and a rainy field weekend. The fit is spot-on, the hardware feels premium, and the olive color still looks sharp after a dozen washes.',
            'date' => 'Jul 10, 2026',
            'verified' => true,
            'helpful' => 14,
            'status' => 'published',
        ],
        [
            'id' => 'review-2',
            'product' => 'Patriot Canvas Rucksack',
            'category' => 'Outdoor Gear',
            'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&q=70&auto=format&fit=crop',
            'href' => route('shop'),
            'order' => '#VS-10396',
            'rating' => 4,
            'title' => 'Tough pack with thoughtful details',
            'body' => 'Great everyday carry for work and trail. Straps are comfortable even when loaded. Only wish the interior laptop sleeve was slightly padded — otherwise an excellent buy.',
            'date' => 'Jun 24, 2026',
            'verified' => true,
            'helpful' => 9,
            'status' => 'published',
        ],
        [
            'id' => 'review-3',
            'product' => 'Everyday Leather Wallet',
            'category' => 'Everyday Carry',
            'image' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&q=70&auto=format&fit=crop',
            'href' => route('shop'),
            'order' => '#VS-10311',
            'rating' => 5,
            'title' => 'Minimal, durable, and ages beautifully',
            'body' => 'The leather is thick without being bulky. Card slots broke in quickly and the bronze rivet is a nice touch. Already getting compliments at the VFW.',
            'date' => 'Jun 8, 2026',
            'verified' => true,
            'helpful' => 6,
            'status' => 'published',
        ],
        [
            'id' => 'review-4',
            'product' => 'Sentinel Field Watch',
            'category' => 'Accessories',
            'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=400&q=70&auto=format&fit=crop',
            'href' => route('shop'),
            'order' => '#VS-10249',
            'rating' => 4,
            'title' => 'Reliable field piece with classic styling',
            'body' => 'Keeps excellent time and the lume is strong enough for early morning PT. Band runs a touch stiff out of the box but softened up within a week.',
            'date' => 'May 20, 2026',
            'verified' => true,
            'helpful' => 3,
            'status' => 'published',
        ],
    ];

    $avgRating = round(collect($reviews)->avg('rating'), 1);
    $totalHelpful = collect($reviews)->sum('helpful');
    $filters = ['All', '5 stars', '4 stars', '3 stars & below'];
@endphp

<x-layouts.app title="My Reviews" description="Manage your Valor Supply Co. product reviews — edit, delete, and track helpful votes.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-reviews>

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
                        <p class="mt-2 text-navy-600">Products you’ve shared feedback on — help fellow veterans shop with confidence.</p>
                    </div>
                    <x-ui.button variant="outline" size="sm" :href="route('shop')" class="shrink-0">
                        Browse products to review
                    </x-ui.button>
                </div>

                {{-- Summary stats --}}
                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-card bg-surface p-5 shadow-soft">
                        <p class="text-sm font-medium text-navy-500">Total reviews</p>
                        <p class="mt-1 font-display text-2xl font-bold text-navy-900" data-reviews-total>{{ count($reviews) }}</p>
                    </div>
                    <div class="rounded-card bg-surface p-5 shadow-soft">
                        <p class="text-sm font-medium text-navy-500">Average rating</p>
                        <div class="mt-1 flex items-center gap-2">
                            <p class="font-display text-2xl font-bold text-navy-900" data-reviews-avg>{{ $avgRating }}</p>
                            <x-ui.rating :value="$avgRating" size="sm" data-reviews-avg-stars />
                        </div>
                    </div>
                    <div class="rounded-card bg-surface p-5 shadow-soft">
                        <p class="text-sm font-medium text-navy-500">Helpful votes received</p>
                        <p class="mt-1 font-display text-2xl font-bold text-navy-900" data-reviews-helpful-total>{{ $totalHelpful }}</p>
                    </div>
                </div>

                {{-- Filters --}}
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
                            <option value="helpful">Most helpful</option>
                            <option value="rating-high">Highest rating</option>
                            <option value="rating-low">Lowest rating</option>
                        </select>
                    </label>
                </div>

                <p class="mt-4 text-sm text-navy-500" data-reviews-count aria-live="polite">{{ count($reviews) }} {{ Str::plural('review', count($reviews)) }}</p>

                {{-- Review list --}}
                <div class="mt-4 space-y-5" data-reviews-list>

                    @foreach ($reviews as $review)
                        <article
                            data-review-card
                            data-review-id="{{ $review['id'] }}"
                            data-rating="{{ $review['rating'] }}"
                            data-helpful="{{ $review['helpful'] }}"
                            data-date="{{ $review['date'] }}"
                            data-title="{{ $review['title'] }}"
                            data-body="{{ $review['body'] }}"
                            data-product="{{ $review['product'] }}"
                            class="overflow-hidden rounded-card bg-surface shadow-soft transition-all duration-300 ease-out hover:shadow-card"
                        >
                            <div class="flex flex-col sm:flex-row">
                                {{-- Product image --}}
                                <a href="{{ $review['href'] }}" class="group relative shrink-0 sm:w-44 lg:w-52">
                                    <div class="aspect-4/3 overflow-hidden bg-navy-50 sm:aspect-auto sm:h-full sm:min-h-44">
                                        <img src="{{ $review['image'] }}" alt="{{ $review['product'] }}"
                                             class="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                                             loading="lazy" width="400" height="300">
                                    </div>
                                    <span class="absolute inset-0 bg-navy-900/0 transition-colors duration-200 group-hover:bg-navy-900/10" aria-hidden="true"></span>
                                </a>

                                <div class="flex flex-1 flex-col p-6 sm:p-7">
                                    {{-- Product meta --}}
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <a href="{{ $review['href'] }}" class="font-display text-lg font-bold text-navy-900 transition-colors duration-200 hover:text-bronze-600">{{ $review['product'] }}</a>
                                            <p class="mt-0.5 text-sm text-navy-500">{{ $review['category'] }} · Order {{ $review['order'] }}</p>
                                        </div>
                                        <div data-review-rating-display>
                                            <x-ui.rating :value="$review['rating']" size="sm" />
                                        </div>
                                    </div>

                                    {{-- Review content --}}
                                    <h2 class="mt-4 font-display text-base font-semibold text-navy-900" data-review-title>{{ $review['title'] }}</h2>
                                    <p class="mt-2 text-sm leading-relaxed text-navy-600" data-review-body>{{ $review['body'] }}</p>

                                    {{-- Footer --}}
                                    <div class="mt-auto flex flex-wrap items-center justify-between gap-4 pt-6">
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-navy-500">
                                            <time datetime="{{ $review['date'] }}" data-review-date>{{ $review['date'] }}</time>
                                            @if ($review['verified'])
                                                <x-ui.badge variant="success">
                                                    <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                                    Verified purchase
                                                </x-ui.badge>
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap items-center gap-3">
                                            {{-- Helpful votes --}}
                                            <span class="inline-flex items-center gap-2 rounded-xl border border-navy-200 bg-canvas px-3 py-2 text-xs font-semibold text-navy-700"
                                                  aria-label="{{ $review['helpful'] }} people found this review helpful">
                                                <svg class="size-4 text-olive-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M7 10v12"/><path d="M15.5 4.5a2 2 0 0 1 2 2v4.5H22l-1.5 8.5a2 2 0 0 1-2 1.5H7"/>
                                                </svg>
                                                <span data-helpful-count>{{ $review['helpful'] }}</span>
                                                <span class="hidden sm:inline">found helpful</span>
                                            </span>

                                            <div class="flex gap-2">
                                                <button type="button" data-review-edit aria-label="Edit review for {{ $review['product'] }}"
                                                        class="inline-flex items-center gap-1.5 rounded-xl border border-navy-200 bg-surface px-3 py-2 text-xs font-semibold text-navy-900 transition-colors duration-200 hover:border-navy-300 hover:bg-navy-50">
                                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                                    </svg>
                                                    Edit
                                                </button>
                                                <button type="button" data-review-delete aria-label="Delete review for {{ $review['product'] }}"
                                                        class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-surface px-3 py-2 text-xs font-semibold text-red-600 transition-colors duration-200 hover:border-red-300 hover:bg-red-50">
                                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Empty state --}}
                <div data-reviews-empty hidden class="mt-8 rounded-card border border-navy-100 bg-surface p-12 text-center shadow-soft">
                    <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-navy-900/5 text-navy-500">
                        <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 2.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.3l-5.8 3.1 1.1-6.5L2.6 9.3l6.5-.9L12 2.5Z"/>
                        </svg>
                    </span>
                    <h2 class="mt-5 font-display text-xl font-bold text-navy-900">No reviews yet</h2>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-navy-600">Share your experience with gear you’ve purchased — your feedback helps the community.</p>
                    <x-ui.button variant="primary" size="sm" :href="route('shop')" class="mt-6">Shop and review</x-ui.button>
                </div>

                {{-- Filter empty --}}
                <div data-reviews-filter-empty hidden class="mt-8 rounded-card border border-navy-100 bg-surface p-10 text-center shadow-soft">
                    <p class="text-sm text-navy-600">No reviews match this filter. Try a different rating or clear filters.</p>
                    <button type="button" data-reviews-clear-filter class="mt-4 text-sm font-semibold text-bronze-600 underline-offset-4 hover:underline">Show all reviews</button>
                </div>

                <p class="mt-8 text-sm text-navy-500" data-reviews-status aria-live="polite"></p>
            </div>
        </div>

        {{-- Edit review dialog --}}
        <dialog data-review-dialog class="fixed inset-0 m-auto h-fit max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-card border-0 bg-surface p-0 shadow-glass backdrop:bg-navy-900/40 open:animate-scale-in">
            <form method="dialog" class="p-7" data-review-form novalidate>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-display text-xl font-bold text-navy-900">Edit review</h2>
                        <p class="mt-1 text-sm text-navy-500" data-edit-product-name>Ranger Field Jacket</p>
                    </div>
                    <button type="button" data-review-dialog-close aria-label="Close dialog"
                            class="flex size-9 shrink-0 items-center justify-center rounded-xl text-navy-500 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-6 space-y-5">
                    <fieldset>
                        <legend class="text-sm font-medium text-navy-900">Your rating</legend>
                        <div class="mt-2 flex gap-1" role="radiogroup" aria-label="Rating" data-rating-picker>
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" data-rating-star="{{ $i }}" aria-label="Rate {{ $i }} out of 5 stars"
                                        class="rounded-lg p-1 text-navy-200 transition-colors duration-150 hover:text-bronze-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bronze-500">
                                    <svg class="size-8" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M12 2.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.3l-5.8 3.1 1.1-6.5L2.6 9.3l6.5-.9L12 2.5Z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" value="5" data-rating-value>
                    </fieldset>

                    <div>
                        <label for="review-title" class="block text-sm font-medium text-navy-900">Review title</label>
                        <input type="text" id="review-title" name="title" required maxlength="120"
                               class="mt-1.5 block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                    </div>

                    <div>
                        <label for="review-body" class="block text-sm font-medium text-navy-900">Your review</label>
                        <textarea id="review-body" name="body" required rows="5" maxlength="2000"
                                  class="mt-1.5 block w-full resize-y rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm leading-relaxed text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500"></textarea>
                        <p class="mt-1.5 text-xs text-navy-400">Share what you liked, how it fits, and how you use it.</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap justify-end gap-3">
                    <x-ui.button variant="outline" size="sm" type="button" data-review-dialog-close>Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit">
                        <span data-review-save-label>Save review</span>
                    </x-ui.button>
                </div>
            </form>
        </dialog>

        {{-- Delete confirmation --}}
        <dialog data-review-delete-dialog class="fixed inset-0 m-auto h-fit w-full max-w-md rounded-card border-0 bg-surface p-0 shadow-glass backdrop:bg-navy-900/40 open:animate-scale-in">
            <div class="p-7">
                <div class="flex size-12 items-center justify-center rounded-full bg-red-50 text-red-600">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    </svg>
                </div>
                <h2 class="mt-5 font-display text-xl font-bold text-navy-900">Delete this review?</h2>
                <p class="mt-2 text-sm text-navy-600">This permanently removes your review for <strong data-delete-product>this product</strong>. Helpful votes will be lost.</p>
                <div class="mt-8 flex flex-wrap justify-end gap-3">
                    <x-ui.button variant="outline" size="sm" type="button" data-delete-cancel>Keep review</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="button" data-delete-confirm class="!bg-red-600 hover:!bg-red-700">Delete review</x-ui.button>
                </div>
            </div>
        </dialog>
    </div>

</x-layouts.app>
