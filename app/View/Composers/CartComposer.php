<?php

namespace App\View\Composers;

use App\Services\CartService;
use Illuminate\View\View;

class CartComposer
{
    public function __construct(
        private CartService $cart,
    ) {}

    public function compose(View $view): void
    {
        $view->with('cartItemCount', $this->cart->itemCount());
    }
}
