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
        $summary = $this->compare->summary();

        $view->with('compareItemCount', $summary->itemCount);
        $view->with('compareProductIds', $summary->productIds());
        $view->with('compareMaxItems', $this->compare->maxItems());
    }
}
