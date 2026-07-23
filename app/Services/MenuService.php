<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    private const CACHE_KEY = 'navigation.menus';

    private const CACHE_TTL = 600;

    /**
     * @return array{primary: Collection<int, MenuItem>, footer: array<string, array{name: string, items: Collection<int, MenuItem>}>}
     */
    public function navigation(): array
    {
        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached) && array_key_exists('primary', $cached)) {
            return $this->hydrateNavigation($cached);
        }

        if ($cached !== null) {
            Cache::forget(self::CACHE_KEY);
        }

        $navigation = $this->loadNavigation();

        Cache::put(self::CACHE_KEY, $this->serializeNavigation($navigation), self::CACHE_TTL);

        return $navigation;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array{primary: Collection<int, MenuItem>, footer: array<string, array{name: string, items: Collection<int, MenuItem>}>}
     */
    private function loadNavigation(): array
    {
        $menus = Menu::query()
            ->active()
            ->with(['allItems' => fn ($query) => $query->active()->with(['children' => fn ($children) => $children->active()->ordered()])])
            ->get()
            ->keyBy('slug');

        $primary = $menus->get('primary');

        $primaryItems = $primary
            ? $primary->allItems->whereNull('parent_id')->values()
            : collect();

        $footer = [];

        foreach (['footer-shop', 'footer-company', 'footer-support', 'footer-legal'] as $slug) {
            $menu = $menus->get($slug);

            if (! $menu) {
                continue;
            }

            $footer[$slug] = [
                'name' => $menu->name,
                'items' => $menu->allItems->whereNull('parent_id')->values(),
            ];
        }

        return [
            'primary' => $primaryItems,
            'footer' => $footer,
        ];
    }

    /**
     * @param  array{primary: Collection<int, MenuItem>, footer: array<string, array{name: string, items: Collection<int, MenuItem>}>}  $navigation
     * @return array{primary: list<array{attributes: array<string, mixed>, children: list<array<string, mixed>>}>, footer: array<string, array{name: string, items: list<array{attributes: array<string, mixed>, children: list<array<string, mixed>>}>}>}
     */
    private function serializeNavigation(array $navigation): array
    {
        return [
            'primary' => $navigation['primary']
                ->map(fn (MenuItem $item): array => $this->serializeMenuItem($item))
                ->values()
                ->all(),
            'footer' => collect($navigation['footer'])
                ->map(fn (array $menu): array => [
                    'name' => $menu['name'],
                    'items' => $menu['items']
                        ->map(fn (MenuItem $item): array => $this->serializeMenuItem($item))
                        ->values()
                        ->all(),
                ])
                ->all(),
        ];
    }

    /**
     * @param  array{primary: list<array{attributes: array<string, mixed>, children: list<array<string, mixed>>}>, footer: array<string, array{name: string, items: list<array{attributes: array<string, mixed>, children: list<array<string, mixed>>}>}>}  $cached
     * @return array{primary: Collection<int, MenuItem>, footer: array<string, array{name: string, items: Collection<int, MenuItem>}>}
     */
    private function hydrateNavigation(array $cached): array
    {
        return [
            'primary' => collect($cached['primary'])
                ->map(fn (array $item): MenuItem => $this->hydrateMenuItem($item))
                ->values(),
            'footer' => collect($cached['footer'])
                ->map(fn (array $menu): array => [
                    'name' => $menu['name'],
                    'items' => collect($menu['items'])
                        ->map(fn (array $item): MenuItem => $this->hydrateMenuItem($item))
                        ->values(),
                ])
                ->all(),
        ];
    }

    /**
     * @return array{attributes: array<string, mixed>, children: list<array{attributes: array<string, mixed>, children: list<array<string, mixed>>}>}
     */
    private function serializeMenuItem(MenuItem $item): array
    {
        return [
            'attributes' => $item->getAttributes(),
            'children' => $item->relationLoaded('children')
                ? $item->children
                    ->map(fn (MenuItem $child): array => $this->serializeMenuItem($child))
                    ->values()
                    ->all()
                : [],
        ];
    }

    /**
     * @param  array{attributes: array<string, mixed>, children?: list<array{attributes: array<string, mixed>, children?: list<array<string, mixed>>}>}  $data
     */
    private function hydrateMenuItem(array $data): MenuItem
    {
        $item = (new MenuItem)->newFromBuilder($data['attributes']);
        $item->exists = true;

        $children = collect($data['children'] ?? [])
            ->map(fn (array $child): MenuItem => $this->hydrateMenuItem($child))
            ->values();

        $item->setRelation('children', $children);

        return $item;
    }
}
