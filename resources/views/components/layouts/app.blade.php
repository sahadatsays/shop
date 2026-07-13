<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title . ' — ' . config('app.name') : config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? 'Premium gear and goods crafted with the honor, discipline, and quality of those who served.' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas text-ink antialiased">
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:rounded-xl focus:bg-navy-900 focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white">
        Skip to content
    </a>

    <header data-site-header
            class="glass sticky top-0 z-50 border-b border-navy-900/5 transition-shadow duration-300">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:h-[4.5rem] lg:px-8">
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="{{ config('app.name') }} — Home">
                <span class="flex size-9 items-center justify-center rounded-xl bg-navy-900 text-bronze-400">
                    <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z"/>
                    </svg>
                </span>
                <span class="font-display text-lg font-bold tracking-tight text-navy-900">{{ config('app.name') }}</span>
            </a>

            {{-- Desktop navigation --}}
            <nav class="hidden items-center gap-1 lg:flex" aria-label="Main navigation">
                @foreach (['Shop' => '#', 'Collections' => '#', 'Our Story' => '#', 'Support' => '#'] as $label => $href)
                    <a href="{{ $href }}"
                       class="rounded-xl px-4 py-2 text-sm font-medium text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-1.5">
                <button type="button" aria-label="Search products"
                        class="flex size-10 items-center justify-center rounded-xl text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                    </svg>
                </button>
                <button type="button" aria-label="Account"
                        class="hidden size-10 items-center justify-center rounded-xl text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900 sm:flex">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>
                    </svg>
                </button>
                <button type="button" aria-label="Cart, 0 items"
                        class="relative flex size-10 items-center justify-center rounded-xl text-navy-700 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                    </svg>
                    <span class="absolute -top-0.5 -right-0.5 flex size-4.5 items-center justify-center rounded-full bg-bronze-500 text-[0.65rem] font-bold text-white">0</span>
                </button>

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

        {{-- Mobile navigation --}}
        <nav id="mobile-nav" data-mobile-nav hidden class="border-t border-navy-900/5 px-4 pt-2 pb-4 lg:hidden" aria-label="Mobile navigation">
            @foreach (['Shop' => '#', 'Collections' => '#', 'Our Story' => '#', 'Support' => '#'] as $label => $href)
                <a href="{{ $href }}"
                   class="block rounded-xl px-4 py-3 text-base font-medium text-navy-800 transition-colors duration-200 hover:bg-navy-900/5">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </header>

    <main id="main-content">
        {{ $slot }}
    </main>

    <footer class="mt-24 bg-navy-900 text-navy-200">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 md:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex size-9 items-center justify-center rounded-xl bg-white/10 text-bronze-400">
                            <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z"/>
                            </svg>
                        </span>
                        <span class="font-display text-lg font-bold text-white">{{ config('app.name') }}</span>
                    </div>
                    <p class="max-w-xs text-sm leading-relaxed text-navy-300">
                        Premium gear and goods crafted with the honor, discipline, and quality of those who served.
                    </p>
                </div>

                @foreach ([
                    'Shop' => ['All Products', 'Apparel', 'Outdoor Gear', 'Everyday Carry', 'Gift Cards'],
                    'Company' => ['Our Story', 'Veteran Owned', 'Giving Back', 'Careers'],
                    'Support' => ['Contact Us', 'Shipping & Returns', 'FAQ', 'Warranty'],
                ] as $heading => $links)
                    <nav aria-label="{{ $heading }} links">
                        <h3 class="font-display text-sm font-semibold tracking-wide text-white uppercase">{{ $heading }}</h3>
                        <ul class="mt-4 space-y-2.5">
                            @foreach ($links as $link)
                                <li>
                                    <a href="#" class="text-sm text-navy-300 transition-colors duration-200 hover:text-bronze-400">{{ $link }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endforeach
            </div>

            <div class="mt-14 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 sm:flex-row">
                <p class="text-sm text-navy-400">&copy; {{ date('Y') }} {{ config('app.name') }} All rights reserved.</p>
                <p class="flex items-center gap-2 text-sm text-navy-400">
                    <svg class="size-4 text-bronze-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z"/>
                    </svg>
                    Proudly veteran owned &amp; operated
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
