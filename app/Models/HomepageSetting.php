<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'featured_products_limit',
        'new_arrivals_limit',
        'best_sellers_limit',
        'brands_limit',
        'categories_limit',
        'reviews_limit',
        'new_badge_days',
        'hide_out_of_stock',
        'sections',
        'popular_searches',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'featured_products_limit' => 'integer',
            'new_arrivals_limit' => 'integer',
            'best_sellers_limit' => 'integer',
            'brands_limit' => 'integer',
            'categories_limit' => 'integer',
            'reviews_limit' => 'integer',
            'new_badge_days' => 'integer',
            'hide_out_of_stock' => 'boolean',
            'sections' => 'array',
            'popular_searches' => 'array',
        ];
    }

    /**
     * @return list<array{key: string, enabled: bool}>
     */
    public static function defaultSections(): array
    {
        return [
            ['key' => 'hero', 'enabled' => true],
            ['key' => 'flash_sale', 'enabled' => true],
            ['key' => 'categories', 'enabled' => true],
            ['key' => 'featured_collections', 'enabled' => true],
            ['key' => 'featured_products', 'enabled' => true],
            ['key' => 'new_arrivals', 'enabled' => true],
            ['key' => 'best_sellers', 'enabled' => true],
            ['key' => 'promo_banners', 'enabled' => true],
            ['key' => 'why_shop', 'enabled' => true],
            ['key' => 'reviews', 'enabled' => true],
            ['key' => 'brands', 'enabled' => true],
            ['key' => 'newsletter', 'enabled' => true],
        ];
    }

    public function isSectionEnabled(string $key): bool
    {
        $sections = $this->sections ?? self::defaultSections();

        foreach ($sections as $section) {
            if (($section['key'] ?? null) === $key) {
                return (bool) ($section['enabled'] ?? false);
            }
        }

        return true;
    }

    /**
     * @return list<string>
     */
    public function enabledSectionKeys(): array
    {
        $sections = $this->sections ?? self::defaultSections();

        return collect($sections)
            ->filter(fn (array $section): bool => (bool) ($section['enabled'] ?? false))
            ->pluck('key')
            ->values()
            ->all();
    }
}
