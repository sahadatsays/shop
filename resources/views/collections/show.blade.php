<x-layouts.app :title="$collection->name" :description="$collection->description ??
    'Shop curated products from the ' . $collection->name . ' collection at Jackpot BD LTD'">

    <div class="border-b border-navy-100 bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <nav class="mb-4 text-sm text-navy-500" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="transition-colors hover:text-navy-900">Home</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('shop') }}" class="transition-colors hover:text-navy-900">Shop</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-navy-900">{{ $collection->name }}</li>
                </ol>
            </nav>

            <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">{{ $collection->name }}</h1>
            @if ($collection->description)
                <p class="mt-3 max-w-2xl text-lg text-navy-600">{{ $collection->description }}</p>
            @endif
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        @if ($collection->products->isEmpty())
            <div class="py-20 text-center">
                <p class="font-display text-xl font-bold text-navy-900">No products in this collection yet</p>
                <p class="mt-2 text-navy-600">Check back soon — new gear is always in the pipeline.</p>
                <x-ui.button :href="route('shop')" variant="secondary" class="mt-8">Browse all products</x-ui.button>
            </div>
        @else
            <p class="text-sm text-navy-500">{{ $collection->products->count() }}
                {{ Str::plural('product', $collection->products->count()) }}</p>

            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($collection->products as $product)
                    @php
                        $badge = $product->shopBadge();
                    @endphp
                    <x-ui.product-card :name="$product->name" :brand="$product->brand?->name" :category="$product->category?->name" :short-description="$product->short_description"
                        :price="$product->formattedPrice()" :old-price="$product->isOnSale() ? $product->formattedCompareAtPrice() : null" :badge="$badge['badge']" :badge-variant="$badge['variant']" :rating="$product->placeholderRating()"
                        :reviews="$product->placeholderReviewCount()" :stock="$product->shopStockLabel()" :stock-percent="$product->shopStockPercent()" :image="$product->primaryImageUrl()" :href="route('product.show', $product)"
                        :product-id="$product->id" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
