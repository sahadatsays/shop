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
        $view->with('wishlistItemCount', $this->wishlist->itemCount());
        $view->with('wishlistProductIds', $this->wishlist->productIds());
    }
}
