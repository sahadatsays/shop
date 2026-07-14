@php
    $stats = [
        ['label' => 'Total orders', 'value' => '24', 'trend' => '+3 this quarter', 'up' => true, 'icon' => 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7ZM9 10V6a3 3 0 0 1 6 0v4', 'tone' => 'bg-navy-900 text-bronze-400'],
        ['label' => 'In transit', 'value' => '2', 'trend' => 'Arriving this week', 'up' => null, 'icon' => 'M3 7h11v8H3zM14 10h4l3 3v2h-7zM7 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm11 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z', 'tone' => 'bg-olive-600 text-white'],
        ['label' => 'Reward points', 'value' => '2,450', 'trend' => '+320 last order', 'up' => true, 'icon' => 'M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 0 2.5 6.5L12 20l-2.5 1.5L12 15Z', 'tone' => 'bg-bronze-500 text-white'],
        ['label' => 'Total saved', 'value' => '$186', 'trend' => 'With member pricing', 'up' => true, 'icon' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm-2-12h3.5a1.5 1.5 0 0 1 0 3H10m1-4v6', 'tone' => 'bg-navy-100 text-navy-700'],
    ];

    $recentOrders = [
        ['number' => '#VS-10482', 'date' => 'Jul 8, 2026', 'items' => 3, 'total' => '$572.40', 'status' => 'In transit', 'statusVariant' => 'bronze', 'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=200&q=70&auto=format&fit=crop'],
        ['number' => '#VS-10396', 'date' => 'Jun 21, 2026', 'items' => 1, 'total' => '$229.00', 'status' => 'Delivered', 'statusVariant' => 'olive', 'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=200&q=70&auto=format&fit=crop'],
        ['number' => '#VS-10311', 'date' => 'Jun 4, 2026', 'items' => 2, 'total' => '$117.00', 'status' => 'Delivered', 'statusVariant' => 'olive', 'image' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=200&q=70&auto=format&fit=crop'],
        ['number' => '#VS-10249', 'date' => 'May 18, 2026', 'items' => 1, 'total' => '$120.00', 'status' => 'Processing', 'statusVariant' => 'navy', 'image' => 'https://images.unsplash.com/photo-1520095972714-909e91b038e5?w=200&q=70&auto=format&fit=crop'],
    ];

    $trackingSteps = [
        ['label' => 'Order placed', 'meta' => 'Jul 8, 09:14', 'state' => 'done'],
        ['label' => 'Packed', 'meta' => 'Jul 9, 14:02', 'state' => 'done'],
        ['label' => 'Shipped', 'meta' => 'Jul 10 · USPS', 'state' => 'current'],
        ['label' => 'Delivered', 'meta' => 'Est. Jul 15', 'state' => 'upcoming'],
    ];

    $quickActions = [
        ['label' => 'Track order', 'icon' => 'M12 21s-6-5.5-6-10a6 6 0 0 1 12 0c0 4.5-6 10-6 10Zm0-7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z'],
        ['label' => 'Start a return', 'icon' => 'M3 12a9 9 0 1 0 3-6.7M3 4v4h4'],
        ['label' => 'Reorder favorites', 'icon' => 'M17 2v4H7a4 4 0 0 0 0 8h1m-1 8v-4h10a4 4 0 0 0 0-8h-1'],
        ['label' => 'Contact support', 'icon' => 'M21 12a9 9 0 1 0-3.5 7.1L21 21l-1-3.4A8.96 8.96 0 0 0 21 12ZM8 10h8M8 14h5'],
        ['label' => 'Gift cards', 'icon' => 'M3 8h18v4H3zM5 12v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-8M12 8v13M12 8s-1.5-4-4-4a2 2 0 0 0 0 4h4Zm0 0s1.5-4 4-4a2 2 0 0 1 0 4h-4Z'],
        ['label' => 'Size guide', 'icon' => 'M3 9h18v6H3zM7 9v3m4-3v2m4-2v3m4-3v2'],
    ];

    $recommended = [
        ['name' => 'Sentinel Field Watch', 'category' => 'Accessories', 'price' => '$229.00', 'oldPrice' => '$279.00', 'badge' => '-18%', 'badgeVariant' => 'danger', 'rating' => 4.7, 'reviews' => 64, 'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Patriot Canvas Rucksack', 'category' => 'Outdoor Gear', 'price' => '$149.00', 'oldPrice' => null, 'badge' => null, 'badgeVariant' => 'bronze', 'rating' => 4.8, 'reviews' => 87, 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Everyday Leather Wallet', 'category' => 'Everyday Carry', 'price' => '$79.00', 'oldPrice' => null, 'badge' => null, 'badgeVariant' => 'bronze', 'rating' => 4.8, 'reviews' => 96, 'image' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Garrison Heritage Tee', 'category' => 'Apparel', 'price' => '$38.00', 'oldPrice' => null, 'badge' => 'New', 'badgeVariant' => 'olive', 'rating' => 4.6, 'reviews' => 53, 'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&q=70&auto=format&fit=crop'],
    ];
@endphp

<x-layouts.app title="My Account" description="Your Valor Supply Co. dashboard — orders, rewards, saved gear, and account settings in one place.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">

            {{-- ============ Sidebar ============ --}}
            <x-account.sidebar active="Dashboard" />

            {{-- ============ Main ============ --}}
            <div class="space-y-8 lg:col-span-3">

                {{-- Welcome card --}}
                <section class="relative overflow-hidden rounded-card bg-navy-900 p-8 text-white sm:p-10" aria-labelledby="welcome-heading">
                    <div class="absolute -top-24 -right-24 size-72 rounded-full bg-olive-600/30 blur-3xl" aria-hidden="true"></div>
                    <div class="absolute -bottom-28 right-24 size-56 rounded-full bg-bronze-500/20 blur-3xl" aria-hidden="true"></div>
                    <div class="relative">
                        <p class="text-sm font-medium text-navy-200">Monday, July 13</p>
                        <h1 id="welcome-heading" class="mt-2 font-display text-2xl font-bold sm:text-3xl">Welcome back, James</h1>
                        <p class="mt-3 max-w-lg text-navy-200">
                            Your Ranger Field Jacket is on the move — arriving Wednesday. You're also
                            <strong class="font-semibold text-bronze-400">550 points</strong> away from Platinum.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <x-ui.button variant="accent" size="sm">Track my order</x-ui.button>
                            <x-ui.button href="{{ route('shop') }}" size="sm" class="bg-white/10! text-white! hover:bg-white/20!">Shop new arrivals</x-ui.button>
                        </div>
                    </div>
                </section>

                {{-- Analytics cards --}}
                <section aria-label="Account overview">
                    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                        @foreach ($stats as $stat)
                            <div class="rounded-card bg-surface p-5 shadow-soft transition-shadow duration-300 hover:shadow-card">
                                <span class="flex size-10 items-center justify-center rounded-xl {{ $stat['tone'] }}">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="{{ $stat['icon'] }}"/>
                                    </svg>
                                </span>
                                <p class="mt-4 font-display text-2xl font-extrabold text-navy-900">{{ $stat['value'] }}</p>
                                <p class="mt-0.5 text-sm text-navy-500">{{ $stat['label'] }}</p>
                                <p class="mt-2 flex items-center gap-1 text-xs font-medium {{ $stat['up'] ? 'text-green-700' : 'text-navy-500' }}">
                                    @if ($stat['up'])
                                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
                                    @endif
                                    {{ $stat['trend'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="grid grid-cols-1 gap-8 xl:grid-cols-5">
                    {{-- Recent orders --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft xl:col-span-3" aria-labelledby="orders-heading">
                        <div class="flex items-center justify-between gap-4">
                            <h2 id="orders-heading" class="font-display text-lg font-bold text-navy-900">Recent orders</h2>
                            <a href="{{ route('account.orders') }}" class="text-sm font-medium text-olive-700 underline-offset-4 hover:underline">View all</a>
                        </div>
                        <ul class="mt-5 divide-y divide-navy-100">
                            @foreach ($recentOrders as $order)
                                <li class="flex items-center gap-4 py-4">
                                    <img src="{{ $order['image'] }}" alt="" loading="lazy" class="size-12 shrink-0 rounded-xl object-cover">
                                    <div class="min-w-0 flex-1">
                                        <p class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                            <a href="#" class="text-sm font-semibold text-navy-900 hover:text-olive-700">{{ $order['number'] }}</a>
                                            <x-ui.badge :variant="$order['statusVariant']">{{ $order['status'] }}</x-ui.badge>
                                        </p>
                                        <p class="mt-0.5 text-xs text-navy-500">{{ $order['date'] }} · {{ $order['items'] }} {{ $order['items'] === 1 ? 'item' : 'items' }}</p>
                                    </div>
                                    <p class="text-sm font-bold text-navy-900 tabular-nums">{{ $order['total'] }}</p>
                                    <a href="#" aria-label="View order {{ $order['number'] }}"
                                       class="flex size-9 shrink-0 items-center justify-center rounded-xl text-navy-400 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-900">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    <div class="space-y-8 xl:col-span-2">
                        {{-- Order status tracker --}}
                        <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="tracking-heading">
                            <div class="flex items-center justify-between gap-4">
                                <h2 id="tracking-heading" class="font-display text-lg font-bold text-navy-900">Order status</h2>
                                <span class="text-xs font-semibold text-navy-500">#VS-10482</span>
                            </div>
                            <ol class="mt-6 space-y-0">
                                @foreach ($trackingSteps as $step)
                                    <li class="relative flex gap-4 {{ $loop->last ? '' : 'pb-7' }}">
                                        @unless ($loop->last)
                                            <span class="absolute top-7 left-3.75 h-full w-0.5 {{ $step['state'] === 'done' ? 'bg-olive-600' : 'bg-navy-200' }}" aria-hidden="true"></span>
                                        @endunless
                                        <span class="relative flex size-8 shrink-0 items-center justify-center rounded-full
                                            {{ $step['state'] === 'done' ? 'bg-olive-600 text-white' : '' }}
                                            {{ $step['state'] === 'current' ? 'bg-navy-900 text-white ring-4 ring-navy-900/10' : '' }}
                                            {{ $step['state'] === 'upcoming' ? 'border border-navy-200 bg-surface text-navy-400' : '' }}">
                                            @if ($step['state'] === 'done')
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                            @elseif ($step['state'] === 'current')
                                                <span class="size-2 animate-ping rounded-full bg-bronze-400" aria-hidden="true"></span>
                                            @else
                                                <span class="size-2 rounded-full bg-navy-300" aria-hidden="true"></span>
                                            @endif
                                        </span>
                                        <div class="pt-1">
                                            <p class="text-sm font-semibold {{ $step['state'] === 'upcoming' ? 'text-navy-400' : 'text-navy-900' }}">{{ $step['label'] }}</p>
                                            <p class="text-xs text-navy-500">{{ $step['meta'] }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </section>

                        {{-- Reward points --}}
                        <section class="overflow-hidden rounded-card bg-linear-to-br from-bronze-500 to-bronze-700 p-7 text-white shadow-card" aria-labelledby="rewards-heading">
                            <div class="flex items-center justify-between gap-4">
                                <h2 id="rewards-heading" class="font-display text-lg font-bold">Reward points</h2>
                                <svg class="size-6 text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 0 2.5 6.5L12 20l-2.5 1.5L12 15Z"/>
                                </svg>
                            </div>
                            <p class="mt-4 font-display text-4xl font-extrabold">2,450 <span class="text-base font-semibold text-white/70">pts</span></p>
                            <p class="mt-1 text-sm text-white/80">≈ $24.50 toward your next order</p>
                            <div class="mt-5">
                                <div class="flex items-center justify-between text-xs font-medium text-white/80">
                                    <span>Gold</span>
                                    <span>Platinum at 3,000</span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/25">
                                    <div class="h-full w-[82%] rounded-full bg-white" aria-hidden="true"></div>
                                </div>
                            </div>
                            <x-ui.button size="sm" class="mt-6 w-full bg-white! text-bronze-700! hover:bg-bronze-50!">Redeem points</x-ui.button>
                        </section>
                    </div>
                </div>

                {{-- Quick actions --}}
                <section aria-labelledby="actions-heading">
                    <h2 id="actions-heading" class="font-display text-lg font-bold text-navy-900">Quick actions</h2>
                    <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach ($quickActions as $action)
                            <a href="#"
                               class="group flex flex-col items-center gap-3 rounded-card bg-surface p-5 text-center shadow-soft transition-all duration-300 hover:-translate-y-0.5 hover:shadow-card">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-olive-100 text-olive-700 transition-colors duration-200 group-hover:bg-olive-600 group-hover:text-white">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="{{ $action['icon'] }}"/>
                                    </svg>
                                </span>
                                <span class="text-sm font-medium text-navy-800">{{ $action['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>

                {{-- Recommended products --}}
                <section aria-labelledby="recommended-heading" data-reveal>
                    <div class="flex items-center justify-between gap-4">
                        <h2 id="recommended-heading" class="font-display text-lg font-bold text-navy-900">Recommended for you</h2>
                        <a href="{{ route('shop') }}" class="text-sm font-medium text-olive-700 underline-offset-4 hover:underline">Browse the shop</a>
                    </div>
                    <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($recommended as $product)
                            <x-ui.product-card
                                :name="$product['name']"
                                :category="$product['category']"
                                :price="$product['price']"
                                :old-price="$product['oldPrice']"
                                :badge="$product['badge']"
                                :badge-variant="$product['badgeVariant']"
                                :rating="$product['rating']"
                                :reviews="$product['reviews']"
                                :image="$product['image']"
                                :href="route('product.show')"
                            />
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
