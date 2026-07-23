<?php

namespace App\View\Composers;

use App\Support\StoreSettings;
use Illuminate\View\View;

class StoreSettingsComposer
{
    public function compose(View $view): void
    {
        $settings = StoreSettings::current();

        $view->with('storeSettings', $settings);
        $view->with('storeName', $settings->displayName());
        $view->with('storeTagline', $settings->tagline);
        $view->with('storeThemeCss', $settings->themeCssVariables());
    }
}
