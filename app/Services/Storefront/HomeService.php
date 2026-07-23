<?php

namespace App\Services\Storefront;

use App\Enums\PromotionPlacement;
use App\Enums\PromotionType;
use App\Models\Collection;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Support\Collection as SupportCollection;

class HomeService
{
    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return [
            'heroBanner' => $this->heroBanner(),
            'countdownPromotion' => $this->countdownPromotion(),
            'featuredCollections' => $this->featuredCollections(),
            'saleProducts' => $this->saleProducts(),
            'bestSellers' => $this->bestSellers(),
            'activeOffers' => $this->activeOffers(),
        ];
    }

    public function heroBanner(): ?Promotion
    {
        return Promotion::query()
            ->active()
            ->ofType(PromotionType::Banner)
            ->where('placement', PromotionPlacement::HomeHero)
            ->orderBy('sort_order')
            ->first();
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
    public function saleProducts(): SupportCollection
    {
        return Product::query()
            ->published()
            ->onSale()
            ->with(['category', 'images'])
            ->limit(8)
            ->get();
    }

    /**
     * @return SupportCollection<int, Product>
     */
    public function bestSellers(): SupportCollection
    {
        return Product::query()
            ->published()
            ->featured()
            ->with(['category', 'images'])
            ->limit(4)
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
}
