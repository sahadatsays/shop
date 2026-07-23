@props([
    'banners' => collect(),
])

@php
    $slides = collect($banners);
    $fallbackImage = 'https://images.unsplash.com/photo-1508672019048-805c876b67e2?w=2000&q=75&auto=format&fit=crop';
@endphp

<section class="relative overflow-hidden bg-navy-950" data-hero-slider aria-roledescription="carousel" aria-label="Hero banners">
    @if ($slides->isEmpty())
        <div class="relative flex min-h-[85vh] items-center">
            <div class="absolute inset-0" aria-hidden="true">
                <img src="{{ $fallbackImage }}" alt="" fetchpriority="high" class="size-full animate-slow-zoom object-cover opacity-60">
                <div class="absolute inset-0 bg-linear-to-r from-navy-950 via-navy-950/70 to-navy-950/20"></div>
            </div>
            <div class="relative mx-auto w-full max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <x-ui.badge variant="bronze" class="mb-6">Veteran owned &amp; operated since 2019</x-ui.badge>
                    <h1 class="font-display text-4xl font-extrabold tracking-tight text-white sm:text-6xl lg:text-7xl">Honor in every stitch and seam.</h1>
                    <p class="mt-6 max-w-xl text-lg leading-relaxed text-navy-200">Premium apparel, collectibles, and field gear designed by veterans.</p>
                    <div class="mt-10 flex flex-wrap items-center gap-4">
                        <x-ui.button :href="route('shop')" variant="accent" size="lg">Shop best sellers</x-ui.button>
                        <x-ui.button :href="route('about')" size="lg" class="border border-white/20 bg-white/10 text-white backdrop-blur-sm hover:bg-white/20">Our story</x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="relative min-h-[85vh]" data-hero-track>
            @foreach ($slides as $index => $banner)
                @php
                    $desktop = $banner->desktopImageUrl() ?? $fallbackImage;
                    $mobile = $banner->mobileImageUrl() ?? $desktop;
                @endphp
                <article
                    data-hero-slide
                    @class([
                        'absolute inset-0 flex items-center transition-opacity duration-700',
                        'opacity-100 z-10' => $index === 0,
                        'opacity-0 z-0 pointer-events-none' => $index !== 0,
                    ])
                    aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
                >
                    <div class="absolute inset-0" aria-hidden="true">
                        <picture>
                            <source media="(max-width: 768px)" srcset="{{ $mobile }}">
                            <img src="{{ $desktop }}"
                                 alt=""
                                 @if ($index === 0) fetchpriority="high" @else loading="lazy" @endif
                                 class="size-full object-cover opacity-60">
                        </picture>
                        <div class="absolute inset-0 bg-linear-to-r from-navy-950 via-navy-950/70 to-navy-950/20"></div>
                        <div class="absolute -bottom-40 -left-24 size-[30rem] rounded-full bg-olive-600/20 blur-3xl"></div>
                    </div>

                    <div class="relative mx-auto w-full max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
                        <div class="max-w-2xl animate-fade-in-up">
                            @if ($banner->badge_text)
                                <x-ui.badge variant="bronze" class="mb-6">
                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z"/>
                                    </svg>
                                    {{ $banner->badge_text }}
                                </x-ui.badge>
                            @endif

                            <h1 class="font-display text-4xl font-extrabold tracking-tight text-white sm:text-6xl lg:text-7xl">
                                {{ $banner->title }}
                            </h1>

                            @if ($banner->description || $banner->subtitle)
                                <p class="mt-6 max-w-xl text-lg leading-relaxed text-navy-200">
                                    {{ $banner->description ?: $banner->subtitle }}
                                </p>
                            @endif

                            <div class="mt-10 flex flex-wrap items-center gap-4">
                                @if ($banner->primary_label && $banner->primary_url)
                                    <x-ui.button :href="$banner->primary_url" variant="accent" size="lg">
                                        {{ $banner->primary_label }}
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M5 12h14m-6-6 6 6-6 6"/>
                                        </svg>
                                    </x-ui.button>
                                @endif
                                @if ($banner->secondary_label && $banner->secondary_url)
                                    <x-ui.button :href="$banner->secondary_url" size="lg" class="border border-white/20 bg-white/10 text-white backdrop-blur-sm hover:bg-white/20">
                                        {{ $banner->secondary_label }}
                                    </x-ui.button>
                                @endif
                            </div>

                            <dl class="mt-14 grid max-w-lg grid-cols-3 gap-6 border-t border-white/10 pt-8">
                                @foreach ([['38K+', 'Orders shipped'], ['4.9/5', 'Average rating'], ['$212K', 'Given back']] as [$stat, $statLabel])
                                    <div>
                                        <dt class="sr-only">{{ $statLabel }}</dt>
                                        <dd class="font-display text-2xl font-bold text-white sm:text-3xl">{{ $stat }}</dd>
                                        <dd class="mt-1 text-sm text-navy-300">{{ $statLabel }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($slides->count() > 1)
            <div class="absolute inset-x-0 bottom-8 z-20 flex items-center justify-center gap-3">
                <button type="button" data-hero-prev aria-label="Previous slide"
                        class="flex size-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white backdrop-blur-sm transition hover:bg-white/20">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5m6 6-6-6 6-6"/></svg>
                </button>
                <div class="flex gap-2" data-hero-dots role="tablist" aria-label="Hero slides">
                    @foreach ($slides as $index => $banner)
                        <button type="button" data-hero-dot="{{ $index }}" aria-label="Go to slide {{ $index + 1 }}"
                                @class([
                                    'h-1.5 w-8 overflow-hidden rounded-full bg-white/30',
                                    'ring-2 ring-bronze-400' => $index === 0,
                                ])>
                            <span data-hero-progress class="block h-full w-0 bg-bronze-400 transition-[width] duration-100 ease-linear"></span>
                        </button>
                    @endforeach
                </div>
                <button type="button" data-hero-next aria-label="Next slide"
                        class="flex size-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white backdrop-blur-sm transition hover:bg-white/20">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </button>
            </div>
        @endif
    @endif
</section>
