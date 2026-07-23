<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\Admin\AdminMenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class MenuItemController extends Controller
{
    public function __construct(private AdminMenuService $menus) {}

    public function store(MenuItemRequest $request, Menu $menu): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        $this->menus->createItem($menu, $request->validated());

        return redirect()
            ->route('admin.homepage.menus.edit', $menu)
            ->with('success', 'Menu item created successfully.');
    }

    public function update(MenuItemRequest $request, Menu $menu, MenuItem $menuItem): RedirectResponse
    {
        abort_unless($menuItem->menu_id === $menu->id, 404);

        $this->menus->updateItem($menuItem, $request->validated());

        return redirect()
            ->route('admin.homepage.menus.edit', $menu)
            ->with('success', 'Menu item updated successfully.');
    }

    public function destroy(Menu $menu, MenuItem $menuItem): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);
        abort_unless($menuItem->menu_id === $menu->id, 404);

        $this->menus->deleteItem($menuItem);

        return redirect()
            ->route('admin.homepage.menus.edit', $menu)
            ->with('success', 'Menu item deleted successfully.');
    }
}
