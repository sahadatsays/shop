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
            'store_name' => config('store.name', config('app.name', 'Jackpot BD LTD')),
            'tagline' => config('store.tagline'),
            'description' => config('store.description'),
            'email' => config('store.email'),
            'support_email' => config('store.support_email'),
            'phone' => config('store.phone'),
            'address' => config('store.address'),
            'social_links' => config('store.social_links', []),
            'currency' => config('store.currency', 'BDT'),
            'timezone' => config('store.timezone', 'Asia/Dhaka'),
            'mail_from_name' => config('store.mail_from_name'),
            'mail_from_address' => config('store.mail_from_address'),
            'utility_bar_message' => config('store.utility_bar_message'),
            'free_shipping_threshold_cents' => Money::toMinor(config('store.free_shipping_threshold_amount', 2000)),
            'flat_shipping_cents' => Money::toMinor(config('store.flat_shipping_amount', 80)),
            'inside_dhaka_shipping_cents' => Money::toMinor(config('store.inside_dhaka_shipping_amount', 60)),
            'outside_dhaka_shipping_cents' => Money::toMinor(config('store.outside_dhaka_shipping_amount', 120)),
            'meta_title' => config('store.meta_title'),
            'meta_description' => config('store.meta_description'),
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
