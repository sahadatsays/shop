<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\Admin\AdminMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(private AdminMenuService $menus) {}

    public function index(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.view'), 403);

        return view('admin.homepage.menus.index', [
            'title' => 'Menus',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Menus'],
            ],
            'menus' => $this->menus->list(),
        ]);
    }

    public function edit(Menu $menu): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.view'), 403);

        $menu = $this->menus->find($menu->id);

        return view('admin.homepage.menus.edit', [
            'title' => $menu->name,
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Menus', 'href' => route('admin.homepage.menus.index')],
                ['label' => $menu->name],
            ],
            'menu' => $menu,
            'routeOptions' => $this->menus->routeOptions(),
            'topLevelItems' => $menu->allItems->whereNull('parent_id')->values(),
        ]);
    }
}
