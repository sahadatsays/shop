<x-layouts.app>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-navy-900">
        <div class="absolute inset-0" aria-hidden="true">
            <div class="absolute -top-40 -right-40 size-[32rem] rounded-full bg-olive-600/20 blur-3xl"></div>
            <div class="absolute -bottom-48 -left-24 size-[28rem] rounded-full bg-bronze-500/15 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8 lg:py-36">
            <div class="max-w-2xl animate-fade-in-up">
                <x-ui.badge variant="bronze" class="mb-6">
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z"/>
                    </svg>
                    Veteran owned &amp; operated
                </x-ui.badge>

                <h1 class="font-display text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Gear built on honor.<br>
                    <span class="text-bronze-400">Crafted to last.</span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-relaxed text-navy-200">
                    Premium apparel, outdoor gear, and everyday carry — designed by veterans
                    who know what quality and reliability truly mean.
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <x-ui.button href="#featured" variant="accent" size="lg">
                        Shop the collection
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14m-6-6 6 6-6 6"/>
                        </svg>
                    </x-ui.button>
                    <x-ui.button href="#" size="lg" class="border border-white/20 bg-white/10 text-white backdrop-blur-sm hover:bg-white/20">
                        Our story
                    </x-ui.button>
                </div>
            </div>
        </div>

        {{-- Trust bar --}}
        <div class="relative border-t border-white/10">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-6 text-sm text-navy-200 sm:px-6 lg:grid-cols-4 lg:px-8">
                @foreach ([
                    'Free shipping over $75',
                    'Lifetime craftsmanship warranty',
                    '5% of profits support veterans',
                    '30-day hassle-free returns',
                ] as $item)
                    <p class="flex items-center gap-2.5">
                        <svg class="size-4 shrink-0 text-bronze-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m5 13 4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </p>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Categories --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28" data-reveal>
        <x-ui.section-heading
            eyebrow="Shop by category"
            title="Equipped for every mission"
            subtitle="From the trail to the workshop to everyday life — gear that holds up when it matters."
        />

        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['name' => 'Apparel', 'count' => '48 products', 'gradient' => 'from-navy-800 to-navy-600'],
                ['name' => 'Outdoor Gear', 'count' => '36 products', 'gradient' => 'from-olive-800 to-olive-600'],
                ['name' => 'Everyday Carry', 'count' => '24 products', 'gradient' => 'from-bronze-800 to-bronze-600'],
                ['name' => 'Home & Office', 'count' => '19 products', 'gradient' => 'from-navy-700 to-olive-700'],
            ] as $category)
                <a href="#" class="group relative flex aspect-[4/3] flex-col justify-end overflow-hidden rounded-card bg-gradient-to-br {{ $category['gradient'] }} p-6 shadow-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/60 to-transparent" aria-hidden="true"></div>
                    <div class="relative">
                        <h3 class="font-display text-xl font-bold text-white">{{ $category['name'] }}</h3>
                        <p class="mt-1 text-sm text-white/70">{{ $category['count'] }}</p>
                    </div>
                    <span class="absolute top-5 right-5 flex size-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur-sm transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14m-6-6 6 6-6 6"/>
                        </svg>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured products --}}
    <section id="featured" class="bg-surface py-20 lg:py-28" data-reveal>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                <x-ui.section-heading
                    align="left"
                    eyebrow="Featured"
                    title="Best sellers"
                    subtitle="The gear our community reaches for again and again."
                />
                <x-ui.button href="#" variant="outline">View all products</x-ui.button>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <x-ui.product-card name="Ranger Field Jacket" category="Apparel" price="$189.00" badge="Best seller" />
                <x-ui.product-card name="Patriot Canvas Rucksack" category="Outdoor Gear" price="$149.00" badge="New" badge-variant="olive" />
                <x-ui.product-card name="Honor Leather Wallet" category="Everyday Carry" price="$79.00" />
                <x-ui.product-card name="Service Insulated Bottle" category="Outdoor Gear" price="$42.00" badge="Limited" badge-variant="navy" />
            </div>
        </div>
    </section>

    {{-- Mission / values --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28" data-reveal>
        <x-ui.section-heading
            eyebrow="Why choose us"
            title="Built on service, backed by integrity"
        />

        <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ([
                [
                    'title' => 'Veteran craftsmanship',
                    'body' => 'Every product is designed and tested by veterans who demand field-grade durability from the gear they carry.',
                    'icon' => 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z',
                ],
                [
                    'title' => 'Mission-first quality',
                    'body' => 'We source premium materials and back everything with a lifetime craftsmanship warranty. No shortcuts, ever.',
                    'icon' => 'M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm4.3 7.6-5 5a1 1 0 0 1-1.4 0l-2.2-2.2a1 1 0 1 1 1.4-1.4l1.5 1.5 4.3-4.3a1 1 0 0 1 1.4 1.4Z',
                ],
                [
                    'title' => 'Giving back',
                    'body' => 'Five percent of every purchase funds veteran career transition programs and family support services.',
                    'icon' => 'M12 21s-7.5-4.7-9.5-9A5.5 5.5 0 0 1 12 6.5 5.5 5.5 0 0 1 21.5 12c-2 4.3-9.5 9-9.5 9Z',
                ],
            ] as $value)
                <x-ui.card :hover="true" class="p-8">
                    <span class="flex size-12 items-center justify-center rounded-2xl bg-olive-100 text-olive-700">
                        <svg class="size-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="{{ $value['icon'] }}"/>
                        </svg>
                    </span>
                    <h3 class="mt-5 font-display text-lg font-bold text-navy-900">{{ $value['title'] }}</h3>
                    <p class="mt-2 leading-relaxed text-navy-600">{{ $value['body'] }}</p>
                </x-ui.card>
            @endforeach
        </div>
    </section>

    {{-- Newsletter CTA --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="relative overflow-hidden rounded-card bg-gradient-to-br from-olive-800 to-olive-600 px-6 py-16 shadow-card sm:px-12 lg:px-16">
            <div class="absolute -top-24 -right-24 size-72 rounded-full bg-bronze-400/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-xl text-center">
                <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">Join the ranks</h2>
                <p class="mt-4 text-lg text-olive-100">
                    Early access to new drops, exclusive offers, and stories from the veteran community.
                </p>
                <form class="mt-8 flex flex-col gap-3 sm:flex-row" action="#" method="post">
                    <label for="newsletter-email" class="sr-only">Email address</label>
                    <input type="email" id="newsletter-email" name="email" required placeholder="Enter your email"
                           class="w-full rounded-xl border-0 bg-white/95 px-5 py-3.5 text-sm text-ink placeholder:text-navy-400 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-300">
                    <x-ui.button type="submit" variant="accent" class="shrink-0">Subscribe</x-ui.button>
                </form>
                <p class="mt-4 text-sm text-olive-200">No spam. Unsubscribe anytime.</p>
            </div>
        </div>
    </section>
</x-layouts.app>
