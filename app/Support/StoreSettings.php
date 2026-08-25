<?php

namespace App\Support;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Cache;

class StoreSettings
{
    private const CACHE_KEY = 'store.settings';

    public static function current(): StoreSetting
    {
        $cached = Cache::get(self::CACHE_KEY);

        if ($cached instanceof StoreSetting) {
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
    }

    public static function seedDefaults(): StoreSetting
    {
        return StoreSetting::query()->create([
            'store_name' => config('app.name', 'Jackpot BD LTD'),
            'tagline' => 'Honor in every stitch and seam.',
            'description' => 'Premium gear and goods crafted with the honor, discipline, and quality of those who served. Veteran owned and operated since 2019.',
            'email' => 'hello@valorsupply.co',
            'support_email' => 'support@valorsupply.co',
            'phone' => '1-800-VALOR-CO',
            'address' => '2840 Patriot Parkway, Suite 100, Columbus, OH 43215',
            'social_links' => [
                'instagram' => 'https://instagram.com/valorsupplyco',
                'facebook' => 'https://facebook.com/valorsupplyco',
                'youtube' => 'https://youtube.com/@valorsupplyco',
            ],
            'currency' => 'BDT',
            'timezone' => 'America/New_York',
            'mail_from_name' => 'Jackpot BD LTD',
            'mail_from_address' => 'noreply@valorsupply.co',
            'utility_bar_message' => 'Free express shipping on orders over $75 • 5% of profits support veteran programs',
            'free_shipping_threshold_cents' => 7500,
            'meta_title' => 'Jackpot BD LTD — Veteran-Owned Premium Gear',
            'meta_description' => 'Premium apparel, outdoor gear, and collectibles from a veteran-owned store built on honor, quality, and service.',
            'theme_colors' => StoreSetting::defaultThemeColors(),
        ]);
    }

    private static function loadAndCache(): StoreSetting
    {
        $settings = StoreSetting::query()->first() ?? self::seedDefaults();

        Cache::forever(self::CACHE_KEY, $settings->getAttributes());

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function hydrate(array $attributes): StoreSetting
    {
        $settings = (new StoreSetting)->newFromBuilder($attributes);
        $settings->exists = true;

        return $settings;
    }
}
