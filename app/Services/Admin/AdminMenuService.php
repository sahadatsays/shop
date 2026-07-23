<?php

namespace App\Services\Admin;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\MenuService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class AdminMenuService
{
    public function __construct(private MenuService $menus) {}

    /**
     * @return Collection<int, Menu>
     */
    public function list(): Collection
    {
        return Menu::query()
            ->withCount('allItems')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): Menu
    {
        return Menu::query()
            ->with(['allItems' => fn ($query) => $query->with('children')->ordered()])
            ->findOrFail($id);
    }

    /**
     * @return list<string>
     */
    public function routeOptions(): array
    {
        return collect(Route::getRoutes()->getRoutesByName())
            ->keys()
            ->filter(fn (string $name): bool => ! str_starts_with($name, 'admin.')
                && ! str_starts_with($name, 'ignition.')
                && ! str_starts_with($name, 'boost.'))
            ->sort()
            ->values()
            ->all();
    }

    public function createItem(Menu $menu, array $data): MenuItem
    {
        $item = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => $data['parent_id'] ?? null,
            'label' => $data['label'],
            'url' => $data['url'] ?? null,
            'route_name' => $data['route_name'] ?? null,
            'is_external' => (bool) ($data['is_external'] ?? false),
            'open_in_new_tab' => (bool) ($data['open_in_new_tab'] ?? false),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $this->menus->clearCache();

        return $item;
    }

    public function updateItem(MenuItem $menuItem, array $data): MenuItem
    {
        $menuItem->update([
            'parent_id' => $data['parent_id'] ?? null,
            'label' => $data['label'],
            'url' => $data['url'] ?? null,
            'route_name' => $data['route_name'] ?? null,
            'is_external' => (bool) ($data['is_external'] ?? false),
            'open_in_new_tab' => (bool) ($data['open_in_new_tab'] ?? false),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $this->menus->clearCache();

        return $menuItem->refresh();
    }

    public function deleteItem(MenuItem $menuItem): void
    {
        $menuItem->children()->delete();
        $menuItem->delete();
        $this->menus->clearCache();
    }
}
