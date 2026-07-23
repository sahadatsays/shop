<?php

namespace App\View\Composers;

use App\Services\WishlistService;
use Illuminate\View\View;

class WishlistComposer
{
    public function __construct(
        private WishlistService $wishlist,
    ) {}

    public function compose(View $view): void
    {
        $summary = $this->wishlist->summary();

        $view->with('wishlistItemCount', $summary->itemCount);
        $view->with('wishlistProductIds', $summary->productIds());
    }
}
