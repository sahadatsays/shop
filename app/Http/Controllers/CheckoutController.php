<?php

namespace App\Http\Controllers;

use App\Exceptions\Cart\CartValidationException;
use App\Exceptions\Cart\InsufficientStockException;
use App\Exceptions\Cart\InvalidCouponException;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Support\MoneyFormatter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private CheckoutService $checkout,
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

        $pageData = $this->checkout->pageData($summary);

        return view('checkout', [
            'summary' => $summary,
            'shippingMethods' => $pageData['shippingMethods'],
            'taxRate' => $pageData['taxRate'],
            'currencySymbol' => $pageData['currencySymbol'],
        ]);
    }

    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->checkout->placeOrder($request);
        } catch (CartValidationException|InsufficientStockException $exception) {
            return redirect()
                ->route('cart')
                ->withErrors(['cart' => $exception->getMessage()]);
        } catch (InvalidCouponException $exception) {
            return redirect()
                ->route('checkout')
                ->withErrors(['coupon' => $exception->getMessage()]);
        }

        return redirect()
            ->route('checkout.confirmation', $order)
            ->with('success', 'Thank you! Your order '.$order->order_number.' has been placed.');
    }

    public function confirmation(Order $order): View|RedirectResponse
    {
        if (session('checkout_order_id') !== $order->id) {
            return redirect()->route('track-order.create');
        }

        session()->forget('checkout_order_id');

        $order->load(['items.product', 'customer']);

        return view('checkout-confirmation', [
            'order' => $order,
            'formattedSubtotal' => MoneyFormatter::format($order->subtotal_cents),
            'formattedDiscount' => $order->discount_cents > 0 ? MoneyFormatter::format($order->discount_cents) : null,
            'formattedShipping' => $order->shipping_cents === 0 ? 'Free' : MoneyFormatter::format($order->shipping_cents),
            'formattedTax' => MoneyFormatter::format($order->tax_cents),
            'formattedTotal' => MoneyFormatter::format($order->total_cents),
        ]);
    }
}
