<?php

namespace App\View\Composers;

use App\Services\CompareService;
use Illuminate\View\View;

class CompareComposer
{
    public function __construct(
        private CompareService $compare,
    ) {}

    public function compose(View $view): void
    {
        $view->with('compareItemCount', $this->compare->itemCount());
        $view->with('compareProductIds', $this->compare->productIds());
        $view->with('compareMaxItems', $this->compare->maxItems());
    }
}
