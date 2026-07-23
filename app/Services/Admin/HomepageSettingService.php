<?php

namespace App\Services\Admin;

use App\Models\HomepageSetting;
use App\Support\HomepageSettings;

class HomepageSettingService
{
    /**
     * @return array<string, string>
     */
    public function sectionLabels(): array
    {
        return [
            'hero' => 'Hero slider',
            'flash_sale' => 'Flash sale / countdown',
            'categories' => 'Shop by category',
            'featured_collections' => 'Featured collections',
            'featured_products' => 'Featured products',
            'new_arrivals' => 'New arrivals',
            'best_sellers' => 'Best sellers',
            'promo_banners' => 'Promotional banners',
            'why_shop' => 'Why shop with us',
            'reviews' => 'Customer reviews',
            'brands' => 'Brand logos',
            'newsletter' => 'Newsletter',
        ];
    }

    public function get(): HomepageSetting
    {
        return HomepageSettings::current();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(array $data): HomepageSetting
    {
        $settings = HomepageSetting::query()->firstOrFail();

        $sections = collect($this->sectionLabels())
            ->keys()
            ->map(fn (string $key): array => [
                'key' => $key,
                'enabled' => in_array($key, $data['enabled_sections'] ?? [], true),
            ])
            ->values()
            ->all();

        $popularSearches = collect(preg_split('/\s*,\s*/', (string) ($data['popular_searches'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))
            ->filter()
            ->values()
            ->all();

        $settings->update([
            'featured_products_limit' => (int) $data['featured_products_limit'],
            'new_arrivals_limit' => (int) $data['new_arrivals_limit'],
            'best_sellers_limit' => (int) $data['best_sellers_limit'],
            'brands_limit' => (int) $data['brands_limit'],
            'categories_limit' => (int) $data['categories_limit'],
            'reviews_limit' => (int) $data['reviews_limit'],
            'new_badge_days' => (int) $data['new_badge_days'],
            'hide_out_of_stock' => (bool) ($data['hide_out_of_stock'] ?? false),
            'sections' => $sections,
            'popular_searches' => $popularSearches,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
        ]);

        HomepageSettings::clearCache();

        return $settings->refresh();
    }
}
