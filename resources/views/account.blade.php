@php
    /** @var array<string, mixed> $dashboard */
    $welcome = $dashboard['welcome'];
    $stats = $dashboard['stats'];
    $recentOrders = $dashboard['recentOrders'];
    $spotlightOrder = $dashboard['spotlightOrder'];
    $rewards = $dashboard['rewards'];
    $quickActions = $dashboard['quickActions'];
    $recommendedProducts = $dashboard['recommendedProducts'];
@endphp

<x-layouts.app :title="$title"
    description="{{ $welcome['headline'] }}. {{ $welcome['message'] }}. View your recent orders, track shipments, and manage your account settings.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">

            <x-account.sidebar active="Dashboard" />

            <div class="space-y-8 lg:col-span-3">

                {{-- Welcome card --}}
                <section class="relative overflow-hidden rounded-card bg-navy-900 p-8 text-white sm:p-10"
                    aria-labelledby="welcome-heading">
                    <div class="absolute -top-24 -right-24 size-72 rounded-full bg-olive-600/30 blur-3xl"
                        aria-hidden="true"></div>
                    <div class="absolute -bottom-28 right-24 size-56 rounded-full bg-bronze-500/20 blur-3xl"
                        aria-hidden="true"></div>
                    <div class="relative">
                        <p class="text-sm font-medium text-navy-200">{{ $welcome['date_label'] }}</p>
                        <h1 id="welcome-heading" class="mt-2 font-display text-2xl font-bold sm:text-3xl">
                            {{ $welcome['headline'] }}</h1>
                        <p class="mt-3 max-w-lg text-navy-200">
                            {{-- @if ($welcome['highlight'])
                                {!! str_replace(
                                    $welcome['highlight'],
                                    '<strong class="font-semibold text-bronze-400">' . $welcome['highlight'] . '</strong>',
                                    e($welcome['message']),
                                ) !!}
                            @else
                                {{ $welcome['message'] }}
                            @endif --}}
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            @if ($welcome['show_track_cta'])
                                <x-ui.button :href="$welcome['track_url']" variant="accent" size="sm">Track my
                                    order</x-ui.button>
                            @endif
                            <x-ui.button :href="$welcome['shop_url']" size="sm"
                                class="bg-white/10! text-white! hover:bg-white/20!">Shop new arrivals</x-ui.button>
                        </div>
                    </div>
                </section>

                {{-- Analytics cards --}}
                <section aria-label="Account overview">
                    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                        @foreach ($stats as $stat)
                            <div
                                class="rounded-card bg-surface p-5 shadow-soft transition-shadow duration-300 hover:shadow-card">
                                <span class="flex size-10 items-center justify-center rounded-xl {{ $stat['tone'] }}">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <path d="{{ $stat['icon'] }}" />
                                    </svg>
                                </span>
                                <p class="mt-4 font-display text-2xl font-extrabold text-navy-900">{{ $stat['value'] }}
                                </p>
                                <p class="mt-0.5 text-sm text-navy-500">{{ $stat['label'] }}</p>
                                <p @class([
                                    'mt-2 flex items-center gap-1 text-xs font-medium',
                                    'text-green-700' => $stat['up'] === true,
                                    'text-navy-500' => $stat['up'] !== true,
                                ])>
                                    @if ($stat['up'] === true)
                                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            aria-hidden="true">
                                            <path d="M7 17 17 7M9 7h8v8" />
                                        </svg>
                                    @endif
                                    {{ $stat['trend'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="grid grid-cols-1 gap-8 xl:grid-cols-5">
                    {{-- Recent orders --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft xl:col-span-3"
                        aria-labelledby="orders-heading">
                        <div class="flex items-center justify-between gap-4">
                            <h2 id="orders-heading" class="font-display text-lg font-bold text-navy-900">Recent orders
                            </h2>
                            <a href="{{ route('account.orders') }}"
                                class="text-sm font-medium text-olive-700 underline-offset-4 hover:underline">View
                                all</a>
                        </div>

                        @if ($recentOrders === [])
                            <div
                                class="mt-8 rounded-xl border border-dashed border-navy-200 bg-navy-50/50 px-6 py-10 text-center">
                                <p class="font-display text-lg font-bold text-navy-900">No orders yet</p>
                                <p class="mt-2 text-sm text-navy-600">When you place your first order, it will show up
                                    here with tracking and details.</p>
                                <x-ui.button :href="route('shop')" variant="primary" size="sm" class="mt-6">Start
                                    shopping</x-ui.button>
                            </div>
                        @else
                            <ul class="mt-5 divide-y divide-navy-100">
                                @foreach ($recentOrders as $order)
                                    <li class="flex items-center gap-4 py-4">
                                        @if ($order['thumbnail'])
                                            <img src="{{ $order['thumbnail'] }}" alt="" loading="lazy"
                                                class="size-12 shrink-0 rounded-xl object-cover bg-navy-100">
                                        @else
                                            <span
                                                class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-navy-100 text-navy-400"
                                                aria-hidden="true">
                                                <svg class="size-5" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path
                                                        d="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z" />
                                                </svg>
                                            </span>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <p class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                                <a href="{{ $order['track_url'] }}"
                                                    class="text-sm font-semibold text-navy-900 hover:text-olive-700">{{ $order['number'] }}</a>
                                                <x-ui.badge :variant="$order['status_variant']">{{ $order['status'] }}</x-ui.badge>
                                            </p>
                                            <p class="mt-0.5 text-xs text-navy-500">{{ $order['placed'] }} ·
                                                {{ $order['item_count'] }}
                                                {{ $order['item_count'] === 1 ? 'item' : 'items' }}</p>
                                        </div>
                                        <p class="text-sm font-bold text-navy-900 tabular-nums">{{ $order['total'] }}
                                        </p>
                                        <a href="{{ $order['track_url'] }}"
                                            aria-label="View order {{ $order['number'] }}"
                                            class="flex size-9 shrink-0 items-center justify-center rounded-xl text-navy-400 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-900">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                aria-hidden="true">
                                                <path d="m9 6 6 6-6 6" />
                                            </svg>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </section>

                    <div class="space-y-8 xl:col-span-2">
                        {{-- Order status tracker --}}
                        <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="tracking-heading">
                            <div class="flex items-center justify-between gap-4">
                                <h2 id="tracking-heading" class="font-display text-lg font-bold text-navy-900">Order
                                    status</h2>
                                @if ($spotlightOrder)
                                    <a href="{{ $spotlightOrder['track_url'] }}"
                                        class="text-xs font-semibold text-navy-500 hover:text-olive-700">{{ $spotlightOrder['number'] }}</a>
                                @endif
                            </div>

                            @if ($spotlightOrder)
                                <ol class="mt-6 space-y-0">
                                    @foreach ($spotlightOrder['timeline'] as $step)
                                        <li class="relative flex gap-4 {{ $loop->last ? '' : 'pb-7' }}">
                                            @unless ($loop->last)
                                                <span
                                                    class="absolute top-7 left-3.75 h-full w-0.5 {{ $step['state'] === 'done' ? 'bg-olive-600' : 'bg-navy-200' }}"
                                                    aria-hidden="true"></span>
                                            @endunless
                                            <span @class([
                                                'relative flex size-8 shrink-0 items-center justify-center rounded-full',
                                                'bg-olive-600 text-white' => $step['state'] === 'done',
                                                'bg-navy-900 text-white ring-4 ring-navy-900/10' =>
                                                    $step['state'] === 'current',
                                                'border border-navy-200 bg-surface text-navy-400' =>
                                                    $step['state'] === 'upcoming',
                                            ])>
                                                @if ($step['state'] === 'done')
                                                    <svg class="size-4" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2.5"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="m5 13 4 4L19 7" />
                                                    </svg>
                                                @elseif ($step['state'] === 'current')
                                                    <span class="size-2 animate-ping rounded-full bg-bronze-400"
                                                        aria-hidden="true"></span>
                                                @else
                                                    <span class="size-2 rounded-full bg-navy-300"
                                                        aria-hidden="true"></span>
                                                @endif
                                            </span>
                                            <div class="pt-1">
                                                <p @class([
                                                    'text-sm font-semibold',
                                                    'text-navy-400' => $step['state'] === 'upcoming',
                                                    'text-navy-900' => $step['state'] !== 'upcoming',
                                                ])>{{ $step['label'] }}</p>
                                                <p class="text-xs text-navy-500">{{ $step['meta'] }}</p>
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                                <x-ui.button :href="$spotlightOrder['track_url']" variant="outline" size="sm"
                                    class="mt-6 w-full">View order details</x-ui.button>
                            @else
                                <div
                                    class="mt-6 rounded-xl border border-dashed border-navy-200 bg-navy-50/40 px-5 py-8 text-center">
                                    <p class="text-sm font-medium text-navy-900">No active shipments</p>
                                    <p class="mt-1 text-xs text-navy-500">Track your next order here once it ships.</p>
                                </div>
                            @endif
                        </section>

                        {{-- Reward points --}}
                        {{-- <section
                            class="overflow-hidden rounded-card bg-linear-to-br from-bronze-500 to-bronze-700 p-7 text-white shadow-card"
                            aria-labelledby="rewards-heading">
                            <div class="flex items-center justify-between gap-4">
                                <h2 id="rewards-heading" class="font-display text-lg font-bold">Reward points</h2>
                                <svg class="size-6 text-white/70" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 0 2.5 6.5L12 20l-2.5 1.5L12 15Z" />
                                </svg>
                            </div>
                            <p class="mt-4 font-display text-4xl font-extrabold">{{ $rewards['points_label'] }} <span
                                    class="text-base font-semibold text-white/70">pts</span></p>
                            <p class="mt-1 text-sm text-white/80">≈ {{ $rewards['redeemable_value'] }} toward your
                                next order</p>
                            <div class="mt-5">
                                <div class="flex items-center justify-between text-xs font-medium text-white/80">
                                    <span>{{ $rewards['current_tier'] }}</span>
                                    @if ($rewards['next_tier'])
                                        <span>{{ $rewards['next_tier'] }} at
                                            {{ number_format($rewards['next_tier_threshold']) }}</span>
                                    @else
                                        <span>Top tier unlocked</span>
                                    @endif
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/25">
                                    <div class="h-full rounded-full bg-white"
                                        style="width: {{ $rewards['progress_percent'] }}%" aria-hidden="true"></div>
                                </div>
                            </div>
                            <x-ui.button :href="route('support')" size="sm"
                                class="mt-6 w-full bg-white! text-bronze-700! hover:bg-bronze-50!">Redeem
                                points</x-ui.button>
                        </section> --}}
                    </div>
                </div>

                {{-- Quick actions --}}
                <section aria-labelledby="actions-heading">
                    <h2 id="actions-heading" class="font-display text-lg font-bold text-navy-900">Quick actions</h2>
                    <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach ($quickActions as $action)
                            <a href="{{ $action['href'] }}"
                                class="group flex flex-col items-center gap-3 rounded-card bg-surface p-5 text-center shadow-soft transition-all duration-300 hover:-translate-y-0.5 hover:shadow-card">
                                <span
                                    class="flex size-11 items-center justify-center rounded-xl bg-olive-100 text-olive-700 transition-colors duration-200 group-hover:bg-olive-600 group-hover:text-white">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <path d="{{ $action['icon'] }}" />
                                    </svg>
                                </span>
                                <span class="text-sm font-medium text-navy-800">{{ $action['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>

                {{-- Recommended products --}}
                @if ($recommendedProducts->isNotEmpty())
                    <section aria-labelledby="recommended-heading" data-reveal>
                        <div class="flex items-center justify-between gap-4">
                            <h2 id="recommended-heading" class="font-display text-lg font-bold text-navy-900">
                                Recommended for you</h2>
                            <a href="{{ route('shop') }}"
                                class="text-sm font-medium text-olive-700 underline-offset-4 hover:underline">Browse
                                the shop</a>
                        </div>
                        <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($recommendedProducts as $product)
                                @php $badge = $product->shopBadge(); @endphp
                                <x-ui.product-card :name="$product->name" :category="$product->category?->name" :price="$product->formattedPrice()"
                                    :old-price="$product->isOnSale() ? $product->formattedCompareAtPrice() : null" :badge="$badge['badge']" :badge-variant="$badge['variant']" :rating="$product->displayRating()"
                                    :reviews="$product->displayReviewCount()" :image="$product->primaryImageUrl()" :href="route('product.show', $product)" :product-id="$product->id" />
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
