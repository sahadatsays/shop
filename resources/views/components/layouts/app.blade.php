@php
    $pageTitle = isset($title) ? $title.' — '.$storeName : ($storeSettings->meta_title ?? $storeName);
    $pageDescription = $description ?? $storeSettings->defaultMetaDescription();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    @if ($storeSettings->meta_keywords)
        <meta name="keywords" content="{{ $storeSettings->meta_keywords }}">
    @endif
    @if ($storeSettings->ogImageUrl())
        <meta property="og:image" content="{{ $storeSettings->ogImageUrl() }}">
    @endif
    @if ($storeSettings->faviconUrl())
        <link rel="icon" href="{{ $storeSettings->faviconUrl() }}">
    @endif
    @if ($storeSettings->google_analytics_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $storeSettings->google_analytics_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json($storeSettings->google_analytics_id));
        </script>
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (filled($storeThemeCss))
        <style>
            :root {
                @foreach ($storeThemeCss as $variable => $value)
                {{ $variable }}: {{ $value }};
                @endforeach
            }
        </style>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas text-ink antialiased">
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:rounded-xl focus:bg-navy-900 focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white">
        Skip to content
    </a>

    <script type="application/json" data-wishlist-product-ids>@json($wishlistProductIds ?? [])</script>

    @if ($minimal ?? false)
        {{-- Distraction-free header for checkout --}}
        <header class="glass sticky top-0 z-50 border-b border-navy-900/5">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:h-[4.5rem] lg:px-8">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ $storeName }} — Home">
                    @if ($storeSettings->logoUrl())
                        <img src="{{ $storeSettings->logoUrl() }}" alt="" class="h-9 w-auto rounded-lg">
                    @else
                        <span class="flex size-9 items-center justify-center rounded-xl bg-navy-900 text-bronze-400">
                            <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z"/>
                            </svg>
                        </span>
                    @endif
                    <span class="font-display text-lg font-bold tracking-tight text-[var(--store-header-text,var(--color-navy-900))]">{{ $storeName }}</span>
                </a>

                <p class="hidden items-center gap-2 text-sm font-medium text-olive-700 sm:flex">
                    <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                    </svg>
                    Secure checkout
                </p>

                <a href="{{ route('cart') }}"
                   class="flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                    Back to cart
                </a>
            </div>
        </header>
    @else
    {{-- Utility bar --}}
    <div class="bg-[var(--store-utility-bg,var(--color-navy-950))] text-center text-xs font-medium tracking-wide text-[var(--store-utility-text,var(--color-navy-200))]">
        <p class="mx-auto max-w-7xl px-4 py-2 sm:px-6 lg:px-8">
            {{ $storeSettings->utility_bar_message ?? 'Free express shipping on orders over $75 • 5% of profits support veteran programs' }}
        </p>
    </div>

    <header data-site-header
            class="glass sticky top-0 z-50 border-b border-navy-900/5 transition-shadow duration-300"
            style="background-color: color-mix(in srgb, var(--store-header-bg, #ffffff) 92%, transparent);">
        <div class="relative mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:h-[4.5rem] lg:px-8">
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ $storeName }} — Home">
                @if ($storeSettings->logoUrl())
                    <img src="{{ $storeSettings->logoUrl() }}" alt="" class="h-9 w-auto rounded-lg">
                @else
                    <span class="flex size-9 items-center justify-center rounded-xl bg-navy-900 text-bronze-400">
                        <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z"/>
                        </svg>
                    </span>
                @endif
                <span class="font-display text-lg font-bold tracking-tight text-[var(--store-header-text,var(--color-navy-900))]">{{ $storeName }}</span>
            </a>

            {{-- Desktop navigation --}}
            <nav class="hidden items-center gap-1 lg:flex" aria-label="Main navigation">
                {{-- Mega menu --}}
                <div class="group/mega">
                    <button type="button" aria-haspopup="true"
                            class="flex items-center gap-1.5 rounded-xl px-4 py-2 text-sm font-medium text-navy-700 transition-colors duration-200 group-focus-within/mega:bg-navy-900/5 group-hover/mega:bg-navy-900/5 group-hover/mega:text-navy-900 hover:bg-navy-900/5 hover:text-navy-900">
                        Shop
                        <svg class="size-3.5 transition-transform duration-200 group-hover/mega:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <div class="invisible absolute inset-x-0 top-full translate-y-2 pt-2 opacity-0 transition-all duration-200 ease-out group-focus-within/mega:visible group-focus-within/mega:translate-y-0 group-focus-within/mega:opacity-100 group-hover/mega:visible group-hover/mega:translate-y-0 group-hover/mega:opacity-100">
                        <div class="mx-auto max-w-7xl overflow-hidden rounded-b-card bg-surface shadow-card-hover ring-1 ring-navy-900/5">
                            <div class="grid grid-cols-3 gap-0">
                                <div class="col-span-2 grid grid-cols-2 gap-1 p-8">
                                    @foreach ([
                                        ['name' => 'Apparel', 'desc' => 'Jackets, tees & headwear', 'icon' => 'M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z'],
                                        ['name' => 'Military Collectibles', 'desc' => 'Medals, patches & memorabilia', 'icon' => 'M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 0 2.5 6.5L12 20l-2.5 1.5L12 15Z'],
                                        ['name' => 'Outdoor Gear', 'desc' => 'Packs, tools & field equipment', 'icon' => 'M12 3 3 20h18L12 3Zm0 5 5 9H7l5-9Z'],
                                        ['name' => 'Flags', 'desc' => 'Stitched & embroidered colors', 'icon' => 'M5 3v18M5 4h13l-2.5 4L18 12H5'],
                                        ['name' => 'Challenge Coins', 'desc' => 'Unit & commemorative coins', 'icon' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0-4a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z'],
                                        ['name' => 'Books', 'desc' => 'History, memoirs & field guides', 'icon' => 'M5 4h11a3 3 0 0 1 3 3v13H8a3 3 0 0 1-3-3V4Zm0 13h14M9 8h6'],
                                        ['name' => 'Accessories', 'desc' => 'Wallets, watches & EDC', 'icon' => 'M12 8a4 4 0 0 1 4 4v0a4 4 0 0 1-8 0v0a4 4 0 0 1 4-4Zm-2-5h4l1 5H9l1-5Zm0 18h4l1-5H9l1 5Z'],
                                        ['name' => 'Home Decor', 'desc' => 'Prints, signs & barware', 'icon' => 'M3 11 12 3l9 8M6 10v10h12V10'],
                                    ] as $category)
                                        <a href="{{ route('shop') }}" class="group/item flex items-start gap-4 rounded-xl p-4 transition-colors duration-200 hover:bg-navy-50">
                                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-olive-100 text-olive-700 transition-colors duration-200 group-hover/item:bg-olive-600 group-hover/item:text-white">
                                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $category['icon'] }}"/></svg>
                                            </span>
                                            <span>
                                                <span class="block text-sm font-semibold text-navy-900">{{ $category['name'] }}</span>
                                                <span class="mt-0.5 block text-xs text-navy-500">{{ $category['desc'] }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                                <a href="#" class="group/feature relative m-4 overflow-hidden rounded-card">
                                    <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800&q=70&auto=format&fit=crop" alt="Camper van on an open road at sunset" loading="lazy"
                                         class="absolute inset-0 size-full object-cover transition-transform duration-500 group-hover/feature:scale-105">
                                    <span class="absolute inset-0 bg-linear-to-t from-navy-950/80 via-navy-950/20 to-transparent"></span>
                                    <span class="relative flex h-full min-h-64 flex-col justify-end p-6">
                                        <x-ui.badge variant="bronze" class="self-start">New season</x-ui.badge>
                                        <span class="mt-3 font-display text-lg font-bold text-white">The Expedition Collection</span>
                                        <span class="mt-1 text-sm text-navy-200">Gear for the long way home</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach (['Categories' => route('categories'), 'Our Story' => route('about'), 'Support' => route('support')] as $label => $href)
                    <a href="{{ $href }}"
                       class="rounded-xl px-4 py-2 text-sm font-medium text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-0.5 sm:gap-1">
                <button type="button" data-search-toggle aria-expanded="false" aria-controls="search-panel" aria-label="Search products"
                        class="flex size-10 items-center justify-center rounded-xl text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                    </svg>
                </button>
                <a href="{{ route('wishlist') }}" data-wishlist-link aria-label="Wishlist, {{ $wishlistItemCount ?? 0 }} {{ ($wishlistItemCount ?? 0) === 1 ? 'item' : 'items' }}"
                   class="relative hidden size-10 items-center justify-center rounded-xl text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900 sm:flex">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 21s-7.5-4.7-9.5-9A5.5 5.5 0 0 1 12 6.5 5.5 5.5 0 0 1 21.5 12c-2 4.3-9.5 9-9.5 9Z"/>
                    </svg>
                    <span data-wishlist-count @class(['absolute -top-0.5 -right-0.5 flex size-4.5 items-center justify-center rounded-full bg-bronze-500 text-[0.65rem] font-bold text-white', 'hidden' => ($wishlistItemCount ?? 0) <= 0])>{{ $wishlistItemCount ?? 0 }}</span>
                </a>
                <a href="{{ route('compare') }}" aria-label="Compare products, 3 items"
                   class="relative hidden size-10 items-center justify-center rounded-xl text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900 lg:flex">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M8 3 4 7l4 4M4 7h16M16 21l4-4-4-4M20 17H4"/>
                    </svg>
                    <span class="absolute -top-0.5 -right-0.5 flex size-4.5 items-center justify-center rounded-full bg-olive-600 text-[0.65rem] font-bold text-white">3</span>
                </a>
                <a href="{{ route('account') }}" aria-label="Account"
                   class="hidden size-10 items-center justify-center rounded-xl text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900 sm:flex">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>
                    </svg>
                </a>
                <a href="{{ route('cart') }}" data-cart-link aria-label="Cart, {{ $cartItemCount ?? 0 }} {{ ($cartItemCount ?? 0) === 1 ? 'item' : 'items' }}"
                   class="relative flex size-10 items-center justify-center rounded-xl text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                    </svg>
                    <span data-cart-count @class(['absolute -top-0.5 -right-0.5 flex size-4.5 items-center justify-center rounded-full bg-bronze-500 text-[0.65rem] font-bold text-white', 'hidden' => ($cartItemCount ?? 0) <= 0])>{{ $cartItemCount ?? 0 }}</span>
                </a>

                {{-- Mobile menu toggle --}}
                <button type="button" data-nav-toggle aria-expanded="false" aria-controls="mobile-nav" aria-label="Open menu"
                        class="flex size-10 items-center justify-center rounded-xl text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900 lg:hidden">
                    <svg data-icon-open class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                    <svg data-icon-close class="hidden size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="m6 6 12 12M18 6 6 18"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Search panel --}}
        <div id="search-panel" data-search-panel hidden class="border-t border-navy-900/5">
            <form action="{{ route('search') }}" method="get" class="mx-auto flex max-w-3xl items-center gap-3 px-4 py-4 sm:px-6">
                <svg class="size-5 shrink-0 text-navy-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                </svg>
                <label for="site-search" class="sr-only">Search products</label>
                <input type="search" id="site-search" name="q" data-search-input placeholder="Search jackets, flags, challenge coins…"
                       class="w-full border-0 bg-transparent text-sm text-ink placeholder:text-navy-400 focus:outline-none">
                <x-ui.button type="submit" size="sm">Search</x-ui.button>
            </form>
        </div>

        {{-- Mobile navigation --}}
        <nav id="mobile-nav" data-mobile-nav hidden class="max-h-[70vh] overflow-y-auto border-t border-navy-900/5 px-4 pt-2 pb-4 lg:hidden" aria-label="Mobile navigation">
            @foreach (['Shop' => route('shop'), 'Categories' => route('categories'), 'Our Story' => route('about'), 'Support' => route('support'), 'Wishlist' => route('wishlist'), 'Compare' => route('compare'), 'Account' => route('account')] as $label => $href)
                <a href="{{ $href }}"
                   class="block rounded-xl px-4 py-3 text-base font-medium text-navy-800 transition-colors duration-200 hover:bg-navy-900/5">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </header>
    @endif

    <main id="main-content">
        {{ $slot }}
    </main>

    @if ($minimal ?? false)
        {{-- Slim distraction-free footer for checkout --}}
        <footer class="mt-20 border-t border-navy-100 bg-surface">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-8 sm:px-6 lg:flex-row lg:px-8">
                <p class="text-sm text-navy-500">&copy; {{ date('Y') }} {{ $storeName }}. All rights reserved.</p>
                <ul class="flex flex-wrap justify-center gap-x-6 gap-y-2">
                    @foreach (['Privacy Policy', 'Terms of Service', 'Refund Policy', 'Help'] as $link)
                        <li><a href="#" class="text-sm text-navy-500 transition-colors duration-200 hover:text-navy-900">{{ $link }}</a></li>
                    @endforeach
                </ul>
                <p class="flex items-center gap-2 text-sm text-navy-500">
                    <svg class="size-4 text-bronze-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z"/>
                    </svg>
                    Proudly veteran owned &amp; operated
                </p>
            </div>
        </footer>
    @else
    <footer class="mt-24 bg-[var(--store-footer-bg,var(--color-navy-950))] text-navy-200">
        <div class="mx-auto max-w-7xl px-4 pt-20 pb-10 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-6">
                <div class="space-y-5 lg:col-span-2">
                    <div class="flex items-center gap-2.5">
                        @if ($storeSettings->logoUrl())
                            <img src="{{ $storeSettings->logoUrl() }}" alt="" class="h-10 w-auto rounded-lg">
                        @else
                            <span class="flex size-10 items-center justify-center rounded-xl bg-white/10 text-bronze-400">
                                <svg class="size-5.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z"/>
                                </svg>
                            </span>
                        @endif
                        <span class="font-display text-xl font-bold text-white">{{ $storeName }}</span>
                    </div>
                    <p class="max-w-sm text-sm leading-relaxed text-navy-300">
                        {{ $storeSettings->description ?? 'Premium gear and goods crafted with the honor, discipline, and quality of those who served.' }}
                    </p>
                    <ul class="space-y-2 text-sm text-navy-300">
                        <li class="flex items-center gap-2.5">
                            <svg class="size-4 text-bronze-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16v12H4zM4 7l8 6 8-6"/></svg>
                            <a href="mailto:{{ $storeSettings->contactEmail() }}" class="transition-colors hover:text-[var(--store-link-accent,var(--color-bronze-400))]">{{ $storeSettings->contactEmail() }}</a>
                        </li>
                        @if ($storeSettings->phone)
                            <li class="flex items-center gap-2.5">
                                <svg class="size-4 text-bronze-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></svg>
                                {{ $storeSettings->phone }}
                            </li>
                        @endif
                    </ul>
                    <div class="flex gap-2">
                        @foreach ([
                            'Instagram' => ['url' => $storeSettings->socialLinks()['instagram'], 'path' => 'M8 3h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8a5 5 0 0 1 5-5Zm4 5.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7ZM17 6.8a.8.8 0 1 0 0 1.6.8.8 0 0 0 0-1.6Z'],
                            'Facebook' => ['url' => $storeSettings->socialLinks()['facebook'], 'path' => 'M14 9h3V6h-3a4 4 0 0 0-4 4v2H7v3h3v6h3v-6h3l1-3h-4v-2a1 1 0 0 1 1-1Z'],
                            'YouTube' => ['url' => $storeSettings->socialLinks()['youtube'], 'path' => 'M21 8s-.2-1.4-.8-2c-.8-.8-1.6-.8-2-.9C15.4 5 12 5 12 5s-3.4 0-6.2.1c-.4.1-1.2.1-2 .9-.6.6-.8 2-.8 2S3 9.6 3 11.2v1.5C3 14.4 3.2 16 3.2 16s.2 1.4.8 2c.8.8 1.8.8 2.2.9 1.6.1 5.8.1 5.8.1s3.4 0 6.2-.1c.4-.1 1.2-.1 2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.3v-1.5C21.2 9.6 21 8 21 8ZM10 14.6V9.4l5 2.6-5 2.6Z'],
                        ] as $network => $social)
                            @if ($social['url'])
                                <a href="{{ $social['url'] }}" aria-label="{{ $network }}" target="_blank" rel="noopener noreferrer"
                                   class="flex size-10 items-center justify-center rounded-xl bg-white/5 text-navy-300 transition-colors duration-200 hover:bg-bronze-500 hover:text-white">
                                    <svg class="size-4.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="{{ $social['path'] }}"/></svg>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                @foreach ([
                    'Shop' => ['All Products', 'Apparel', 'Outdoor Gear', 'Challenge Coins', 'Flags', 'Gift Cards'],
                    'Company' => ['Our Story', 'Veteran Owned', 'Giving Back', 'Wholesale', 'Careers', 'Press'],
                    'Support' => ['Contact Us', 'Shipping & Returns', 'Order Tracking', 'FAQ', 'Warranty', 'Size Guides'],
                    'Legal' => ['Privacy Policy', 'Terms of Service', 'Refund Policy', 'Accessibility'],
                ] as $heading => $links)
                    <nav aria-label="{{ $heading }} links">
                        <h3 class="font-display text-sm font-semibold tracking-wide text-white uppercase">{{ $heading }}</h3>
                        <ul class="mt-5 space-y-3">
                            @foreach ($links as $link)
                                @php
                                    $href = match (true) {
                                        $link === 'Contact Us' => route('contact'),
                                        $link === 'Our Story' => route('about'),
                                        $link === 'Order Tracking' => route('track'),
                                        $link === 'FAQ' => route('support').'#faq',
                                        $link === 'All Products' => route('shop'),
                                        default => '#',
                                    };
                                @endphp
                                <li>
                                    <a href="{{ $href }}" class="text-sm text-navy-300 transition-colors duration-200 hover:text-bronze-400">{{ $link }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endforeach
            </div>

            <div class="mt-16 flex flex-col items-center justify-between gap-6 border-t border-white/10 pt-8 lg:flex-row">
                <p class="text-sm text-navy-400">&copy; {{ date('Y') }} {{ $storeName }}. All rights reserved.</p>
                <ul class="flex flex-wrap justify-center gap-2">
                    @foreach (['VISA', 'Mastercard', 'AMEX', 'PayPal', 'Apple Pay', 'G Pay'] as $method)
                        <li class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-bold tracking-wide text-navy-300">{{ $method }}</li>
                    @endforeach
                </ul>
                <p class="flex items-center gap-2 text-sm text-navy-400">
                    <svg class="size-4 text-bronze-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z"/>
                    </svg>
                    Proudly veteran owned &amp; operated
                </p>
            </div>
        </div>
    </footer>
    @endif
</body>
</html>
