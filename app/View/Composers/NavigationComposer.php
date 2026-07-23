<?php

namespace App\View\Composers;

use App\Services\MenuService;
use Illuminate\View\View;

class NavigationComposer
{
    public function __construct(private MenuService $menus) {}

    public function compose(View $view): void
    {
        $navigation = $this->menus->navigation();

        $view->with('primaryMenuItems', $navigation['primary']);
        $view->with('footerMenus', $navigation['footer']);
    }
}
