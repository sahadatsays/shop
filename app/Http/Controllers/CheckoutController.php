<?php

namespace App\Http\Controllers;

use App\Exceptions\Cart\CartValidationException;
use App\Exceptions\Cart\InsufficientStockException;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
    ) {}

    public function index(): View|RedirectResponse
    {
        try {
            $this->cart->validateCart();
            $this->cart->validateStock();
        } catch (CartValidationException|InsufficientStockException $exception) {
            return redirect()
                ->route('cart')
                ->withErrors(['cart' => $exception->getMessage()]);
        }

        $summary = $this->cart->summary();

        if ($summary->isEmpty()) {
            return redirect()
                ->route('cart')
                ->withErrors(['cart' => 'Your cart is empty. Add items before checking out.']);
        }

        return view('checkout', [
            'summary' => $summary,
        ]);
    }
}
