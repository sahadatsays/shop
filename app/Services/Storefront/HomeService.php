<?php

namespace App\Services\Storefront;

use App\Enums\OrderStatus;
use App\Enums\PromotionType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\HeroBanner;
use App\Models\HomepageFeature;
use App\Models\Offer;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PromoBanner;
use App\Models\Promotion;
use App\Models\Review;
use App\Support\HomepageSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeService
{
    private const CACHE_TTL = 300;

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        $settings = HomepageSettings::current();

        return [
            'homepageSettings' => $settings,
            'enabledSections' => $settings->enabledSectionKeys(),
            'heroBanners' => $settings->isSectionEnabled('hero') ? $this->heroBanners() : collect(),
            'countdownPromotion' => $settings->isSectionEnabled('flash_sale') ? $this->countdownPromotion() : null,
            'categories' => $settings->isSectionEnabled('categories') ? $this->categories() : collect(),
            'featuredCollections' => $settings->isSectionEnabled('featured_collections') ? $this->featuredCollections() : collect(),
            'featuredProducts' => $settings->isSectionEnabled('featured_products') ? $this->featuredProducts() : collect(),
            'newArrivals' => $settings->isSectionEnabled('new_arrivals') ? $this->newArrivals() : collect(),
            'bestSellers' => $settings->isSectionEnabled('best_sellers') ? $this->bestSellers() : collect(),
            'promoBanners' => $settings->isSectionEnabled('promo_banners') ? $this->promoBanners() : collect(),
            'whyShopFeatures' => $settings->isSectionEnabled('why_shop') ? $this->whyShopFeatures() : collect(),
            'reviews' => $settings->isSectionEnabled('reviews') ? $this->reviews() : collect(),
            'brands' => $settings->isSectionEnabled('brands') ? $this->brands() : collect(),
            'saleProducts' => $this->saleProducts(),
            'activeOffers' => $this->activeOffers(),
            'showNewsletter' => $settings->isSectionEnabled('newsletter'),
        ];
    }

    /**
     * @return SupportCollection<int, HeroBanner>
     */
    public function heroBanners(): SupportCollection
    {
        return $this->rememberModelCollection(
            'homepage.hero_banners',
            fn () => HeroBanner::query()
                ->activeNow()
                ->ordered()
                ->get(),
            HeroBanner::class,
        );
    }

    public function countdownPromotion(): ?Promotion
    {
        return Promotion::query()
            ->active()
            ->ofType(PromotionType::Countdown)
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->orderBy('ends_at')
            ->first();
    }

    /**
     * @return SupportCollection<int, Category>
     */
    public function categories(): SupportCollection
    {
        $settings = HomepageSettings::current();

        return $this->rememberModelCollection(
            'homepage.categories',
            function () use ($settings) {
                $query = Category::query()
                    ->active()
                    ->whereNull('parent_id')
                    ->whereHas('products', fn (Builder $builder) => $this->applyStorefrontProductConstraints($builder))
                    ->withCount(['products as products_count' => fn (Builder $builder) => $this->applyStorefrontProductConstraints($builder)])
                    ->ordered()
                    ->limit($settings->categories_limit);

                $featured = (clone $query)->featured()->get();

                if ($featured->isNotEmpty()) {
                    return $featured;
                }

                return $query->get();
            },
            Category::class,
        );
    }

    /**
     * @return SupportCollection<int, Collection>
     */
    public function featuredCollections(): SupportCollection
    {
        return Collection::query()
            ->active()
            ->featured()
            ->orderBy('sort_order')
            ->limit(3)
            ->get();
    }

    /**
     * @return SupportCollection<int, Product>
     */
    public function featuredProducts(): SupportCollection
    {
        $settings = HomepageSettings::current();

        return $this->storefrontProducts()
            ->featured()
            ->ordered()
            ->limit($settings->featured_products_limit)
            ->get();
    }

    /**
     * @return SupportCollection<int, Product>
     */
    public function newArrivals(): SupportCollection
    {
        $settings = HomepageSettings::current();

        return $this->storefrontProducts()
            ->newArrival()
            ->latest('created_at')
            ->limit($settings->new_arrivals_limit)
            ->get();
    }

    /**
     * @return SupportCollection<int, Product>
     */
    public function bestSellers(): SupportCollection
    {
        $settings = HomepageSettings::current();
        $limit = $settings->best_sellers_limit;

        $productIds = OrderItem::query()
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as units_sold'))
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', OrderStatus::Delivered)
            ->groupBy('order_items.product_id')
            ->orderByDesc('units_sold')
            ->limit($limit)
            ->pluck('product_id');

        if ($productIds->isEmpty()) {
            return $this->storefrontProducts()
                ->featured()
                ->ordered()
                ->limit($limit)
                ->get();
        }

        $products = $this->storefrontProducts()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        return $productIds
            ->map(fn ($id) => $products->get($id))
            ->filter()
            ->values();
    }

    /**
     * @return SupportCollection<int, PromoBanner>
     */
    public function promoBanners(): SupportCollection
    {
        return $this->rememberModelCollection(
            'homepage.promo_banners',
            fn () => PromoBanner::query()
                ->active()
                ->ordered()
                ->get(),
            PromoBanner::class,
        );
    }

    /**
     * @return SupportCollection<int, HomepageFeature>
     */
    public function whyShopFeatures(): SupportCollection
    {
        return $this->rememberModelCollection(
            'homepage.features',
            fn () => HomepageFeature::query()
                ->active()
                ->ordered()
                ->get(),
            HomepageFeature::class,
        );
    }

    /**
     * @return SupportCollection<int, Review>
     */
    public function reviews(): SupportCollection
    {
        $settings = HomepageSettings::current();

        $ids = Cache::remember('homepage.reviews', self::CACHE_TTL, fn () => Review::query()
            ->latestApproved()
            ->limit($settings->reviews_limit)
            ->pluck('id')
            ->all());

        if ($ids === []) {
            return collect();
        }

        return Review::query()
            ->with('product')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Review $review): int|false => array_search($review->id, $ids, true))
            ->values();
    }

    /**
     * @return SupportCollection<int, Brand>
     */
    public function brands(): SupportCollection
    {
        $settings = HomepageSettings::current();

        return $this->rememberModelCollection(
            'homepage.brands',
            function () use ($settings) {
                $featured = Brand::query()
                    ->active()
                    ->featured()
                    ->ordered()
                    ->limit($settings->brands_limit)
                    ->get();

                if ($featured->isNotEmpty()) {
                    return $featured;
                }

                return Brand::query()
                    ->active()
                    ->ordered()
                    ->limit($settings->brands_limit)
                    ->get();
            },
            Brand::class,
        );
    }

    /**
     * @return SupportCollection<int, Product>
     */
    public function saleProducts(): SupportCollection
    {
        return $this->storefrontProducts()
            ->onSale()
            ->ordered()
            ->limit(8)
            ->get();
    }

    /**
     * @return SupportCollection<int, Offer>
     */
    public function activeOffers(): SupportCollection
    {
        return Offer::query()
            ->active()
            ->with('discount')
            ->orderBy('sort_order')
            ->limit(3)
            ->get();
    }

    /**
     * @return Builder<Product>
     */
    private function storefrontProducts(): Builder
    {
        $query = Product::query()
            ->published()
            ->whereNotNull('category_id')
            ->with(['category', 'images', 'brand']);

        return $this->applyStorefrontProductConstraints($query);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    private function applyStorefrontProductConstraints(Builder $query): Builder
    {
        if (HomepageSettings::current()->hide_out_of_stock || config('shop.require_in_stock', true)) {
            $query->inStock();
        }

        return $query;
    }

    /**
     * @template TModel of Model
     *
     * @param  callable(): SupportCollection<int, TModel>  $query
     * @param  class-string<TModel>  $modelClass
     * @return SupportCollection<int, TModel>
     */
    private function rememberModelCollection(string $key, callable $query, string $modelClass): SupportCollection
    {
        $cached = Cache::get($key);

        if (is_array($cached)) {
            return $this->hydrateModels($cached, $modelClass);
        }

        if ($cached !== null) {
            Cache::forget($key);
        }

        /** @var SupportCollection<int, TModel> $models */
        $models = $query();

        Cache::put(
            $key,
            $models->map(fn (Model $model): array => $model->getAttributes())->values()->all(),
            self::CACHE_TTL,
        );

        return $models;
    }

    /**
     * @template TModel of Model
     *
     * @param  list<array<string, mixed>>  $rows
     * @param  class-string<TModel>  $modelClass
     * @return SupportCollection<int, TModel>
     */
    private function hydrateModels(array $rows, string $modelClass): SupportCollection
    {
        return collect($rows)->map(function (array $attributes) use ($modelClass) {
            $model = (new $modelClass)->newFromBuilder($attributes);
            $model->exists = true;

            return $model;
        });
    }
}
