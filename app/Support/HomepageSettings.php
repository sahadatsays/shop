<?php

namespace App\Support;

use App\Models\HomepageSetting;
use Illuminate\Support\Facades\Cache;

class HomepageSettings
{
    private const CACHE_KEY = 'homepage.settings';

    public static function current(): HomepageSetting
    {
        $cached = Cache::get(self::CACHE_KEY);

        if ($cached instanceof HomepageSetting) {
            return $cached;
        }

        if (is_array($cached)) {
            return self::hydrate($cached);
        }

        if ($cached !== null) {
            Cache::forget(self::CACHE_KEY);
        }

        return self::loadAndCache();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('homepage.data');
        Cache::forget('homepage.hero_banners');
        Cache::forget('homepage.categories');
        Cache::forget('homepage.brands');
        Cache::forget('homepage.promo_banners');
        Cache::forget('homepage.features');
        Cache::forget('homepage.reviews');
        Cache::forget('navigation.menus');
    }

    public static function seedDefaults(): HomepageSetting
    {
        return HomepageSetting::query()->create([
            'featured_products_limit' => 8,
            'new_arrivals_limit' => 8,
            'best_sellers_limit' => 4,
            'brands_limit' => 8,
            'categories_limit' => 8,
            'reviews_limit' => 6,
            'new_badge_days' => 30,
            'hide_out_of_stock' => true,
            'sections' => HomepageSetting::defaultSections(),
            'popular_searches' => ['jackets', 'flags', 'challenge coins', 'boots', 'packs'],
            'meta_title' => 'Valor Supply Co. — Veteran-Owned Premium Gear',
            'meta_description' => 'Shop premium apparel, outdoor gear, and collectibles from a veteran-owned store built on honor, quality, and service.',
            'meta_keywords' => 'veteran gear, outdoor apparel, challenge coins, flags',
        ]);
    }

    private static function loadAndCache(): HomepageSetting
    {
        $settings = HomepageSetting::query()->first() ?? self::seedDefaults();

        Cache::forever(self::CACHE_KEY, $settings->getAttributes());

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function hydrate(array $attributes): HomepageSetting
    {
        $settings = (new HomepageSetting)->newFromBuilder($attributes);
        $settings->exists = true;

        return $settings;
    }
}
