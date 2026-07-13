@php
    $categories = [
        ['name' => 'Apparel', 'count' => 48, 'image' => 'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=800&q=70&auto=format&fit=crop', 'alt' => 'Rack of garment-dyed apparel'],
        ['name' => 'Military Collectibles', 'count' => 32, 'image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=800&q=70&auto=format&fit=crop', 'alt' => 'Metallic collectible pieces in dramatic light'],
        ['name' => 'Outdoor Gear', 'count' => 36, 'image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&q=70&auto=format&fit=crop', 'alt' => 'Field gear and boots laid out before a trek'],
        ['name' => 'Flags', 'count' => 21, 'image' => 'https://images.unsplash.com/photo-1520095972714-909e91b038e5?w=800&q=70&auto=format&fit=crop', 'alt' => 'American flag waving in the wind'],
        ['name' => 'Challenge Coins', 'count' => 27, 'image' => 'https://images.unsplash.com/photo-1533167649158-6d508895b680?w=800&q=70&auto=format&fit=crop', 'alt' => 'Detailed metal craftsmanship close-up'],
        ['name' => 'Books', 'count' => 54, 'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800&q=70&auto=format&fit=crop', 'alt' => 'Stack of history books and memoirs'],
        ['name' => 'Accessories', 'count' => 45, 'image' => 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=800&q=70&auto=format&fit=crop', 'alt' => 'Minimal field watch on a wrist'],
        ['name' => 'Home Decor', 'count' => 30, 'image' => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=800&q=70&auto=format&fit=crop', 'alt' => 'Warm home decor arrangement'],
    ];

    $trending = [
        ['rank' => 1, 'name' => 'Challenge Coins', 'growth' => '+142% this month', 'image' => 'https://images.unsplash.com/photo-1533167649158-6d508895b680?w=800&q=70&auto=format&fit=crop'],
        ['rank' => 2, 'name' => 'Apparel', 'growth' => '+87% this month', 'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=800&q=70&auto=format&fit=crop'],
        ['rank' => 3, 'name' => 'Flags', 'growth' => '+64% this month', 'image' => 'https://images.unsplash.com/photo-1520095972714-909e91b038e5?w=800&q=70&auto=format&fit=crop'],
    ];

    $recentlyAdded = [
        ['name' => 'Garrison Heritage Tee', 'category' => 'Apparel', 'price' => '$38.00', 'badge' => 'New', 'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Honor EDC Kit', 'category' => 'Everyday Carry', 'price' => '$96.00', 'badge' => 'New', 'image' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Field Manual Collection', 'category' => 'Books', 'price' => '$54.00', 'badge' => 'New', 'image' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=800&q=70&auto=format&fit=crop'],
        ['name' => 'Heritage Automatic Watch', 'category' => 'Accessories', 'price' => '$449.00', 'badge' => 'New', 'image' => 'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=800&q=70&auto=format&fit=crop'],
    ];
@endphp

<x-layouts.app title="Browse Categories" description="Explore every collection at Valor Supply Co. — apparel, collectibles, outdoor gear, flags, challenge coins, books, accessories, and home decor.">

    {{-- ============ Category banner ============ --}}
    <section class="relative overflow-hidden bg-navy-950">
        <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=2000&q=75&auto=format&fit=crop"
             alt="" fetchpriority="high"
             class="absolute inset-0 size-full object-cover opacity-40">
        <div class="absolute inset-0 bg-linear-to-b from-navy-950/40 via-navy-950/60 to-navy-950" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-24 text-center sm:px-6 lg:px-8 lg:py-32">
            <nav aria-label="Breadcrumb" class="animate-fade-in">
                <ol class="flex items-center justify-center gap-2 text-sm text-navy-300">
                    <li><a href="{{ route('home') }}" class="transition-colors duration-200 hover:text-white">Home</a></li>
                    <li aria-hidden="true">/</li>
                    <li aria-current="page" class="font-medium text-white">Categories</li>
                </ol>
            </nav>
            <h1 class="mt-5 animate-fade-in-up font-display text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Browse by category
            </h1>
            <p class="mx-auto mt-5 max-w-xl animate-fade-in-up text-lg leading-relaxed text-navy-200" style="animation-delay: 100ms">
                Eight collections, 293 products, one standard of craftsmanship.
                Find exactly what your next mission calls for.
            </p>
        </div>
    </section>

    {{-- ============ Featured category ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8" data-reveal>
        <a href="{{ route('shop') }}"
           class="group relative block overflow-hidden rounded-card shadow-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover">
            <img src="https://images.unsplash.com/photo-1501554728187-ce583db33af7?w=1800&q=75&auto=format&fit=crop"
                 alt="Hiker with a pack overlooking a mountain valley" loading="lazy"
                 class="h-[28rem] w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105 sm:h-[32rem]">
            <div class="absolute inset-0 bg-linear-to-r from-navy-950/85 via-navy-950/40 to-transparent" aria-hidden="true"></div>

            <div class="absolute inset-0 flex flex-col justify-center p-8 sm:p-14">
                <x-ui.badge variant="bronze" class="self-start">Featured category</x-ui.badge>
                <h2 class="mt-5 max-w-md font-display text-3xl font-bold text-white sm:text-5xl">Outdoor Gear</h2>
                <p class="mt-4 max-w-md text-base leading-relaxed text-navy-200 sm:text-lg">
                    Packs, shelters, tools, and layers proven on hard miles — 36 products
                    built for the backcountry and backed for life.
                </p>
                <span class="mt-8 inline-flex items-center gap-2 self-start rounded-xl bg-white px-6 py-3.5 text-sm font-semibold text-navy-900 shadow-soft transition-all duration-300 group-hover:gap-3.5 group-hover:bg-bronze-500 group-hover:text-white">
                    Explore the collection
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </span>
            </div>
        </a>
    </section>

    {{-- ============ All categories grid ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:pb-20" data-reveal>
        <x-ui.section-heading
            align="left"
            eyebrow="All categories"
            title="Every collection, one standard"
        />

        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('shop') }}"
                   class="group relative block overflow-hidden rounded-card shadow-card transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-card-hover">
                    <div class="aspect-4/5 overflow-hidden">
                        <img src="{{ $category['image'] }}" alt="{{ $category['alt'] }}" loading="lazy"
                             class="size-full object-cover transition-transform duration-700 ease-out group-hover:scale-110">
                    </div>
                    <div class="absolute inset-0 bg-linear-to-t from-navy-950/85 via-navy-950/20 to-transparent transition-colors duration-300 group-hover:from-navy-950/95" aria-hidden="true"></div>

                    <div class="absolute inset-x-0 bottom-0 p-6">
                        <h3 class="font-display text-xl font-bold text-white">{{ $category['name'] }}</h3>
                        <p class="mt-1 text-sm text-navy-200">{{ $category['count'] }} products</p>
                        <span class="mt-4 flex max-w-0 items-center gap-1.5 overflow-hidden text-sm font-semibold whitespace-nowrap text-bronze-400 opacity-0 transition-all duration-500 group-hover:max-w-40 group-hover:opacity-100">
                            Shop now
                            <svg class="size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ============ Trending categories ============ --}}
    <section class="bg-surface py-20 lg:py-28" data-reveal>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-ui.section-heading
                eyebrow="Trending now"
                title="What the community is shopping"
                subtitle="The fastest-growing collections this month, ranked by order volume."
            />

            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($trending as $item)
                    <a href="{{ route('shop') }}"
                       class="group flex items-center gap-5 rounded-card bg-canvas p-5 shadow-soft transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover">
                        <span class="font-display text-4xl font-extrabold text-navy-200 transition-colors duration-300 group-hover:text-bronze-500" aria-hidden="true">
                            {{ str_pad($item['rank'], 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="size-20 shrink-0 overflow-hidden rounded-2xl">
                            <img src="{{ $item['image'] }}" alt="" loading="lazy"
                                 class="size-full object-cover transition-transform duration-500 group-hover:scale-110">
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate font-display text-base font-bold text-navy-900">
                                <span class="sr-only">Trending number {{ $item['rank'] }}: </span>{{ $item['name'] }}
                            </span>
                            <span class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-green-700">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 17 10 11l4 4 6-7M20 8h-5m5 0v5"/></svg>
                                {{ $item['growth'] }}
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ Recently added ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28" data-reveal>
        <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
            <x-ui.section-heading
                align="left"
                eyebrow="Recently added"
                title="Fresh from the workshop"
                subtitle="The newest arrivals across all categories."
            />
            <x-ui.button :href="route('shop')" variant="outline">View all new arrivals</x-ui.button>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($recentlyAdded as $product)
                <x-ui.product-card
                    :name="$product['name']"
                    :category="$product['category']"
                    :price="$product['price']"
                    :badge="$product['badge']"
                    badge-variant="olive"
                    :image="$product['image']"
                    :href="route('product.show')"
                />
            @endforeach
        </div>
    </section>
</x-layouts.app>
