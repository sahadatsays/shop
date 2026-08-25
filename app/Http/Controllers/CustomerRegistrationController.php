<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterCustomerRequest;
use App\Services\CartService;
use App\Services\CustomerAuthService;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerRegistrationController extends Controller
{
    public function __construct(
        private CustomerAuthService $auth,
        private CartService $cart,
        private WishlistService $wishlist,
    ) {}

    public function showRegistrationForm(): View
    {
        $banner = asset('storage/login/login-banner.png');
        return view('register')->with([
            'banner' => $banner,
        ]);
    }

    public function store(RegisterCustomerRequest $request): JsonResponse|RedirectResponse
    {
        $customer = $this->auth->register($request->validated(), $request);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Account created successfully.',
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                ],
                'cart' => [
                    'item_count' => $this->cart->itemCount(),
                ],
                'wishlist' => [
                    'item_count' => $this->wishlist->itemCount(),
                ],
            ], 201);
        }

        return redirect()
            ->route('account')
            ->with('success', 'Welcome to Valor, ' . $customer->name . '.');
    }
}
