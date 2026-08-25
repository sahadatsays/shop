<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomerAuthException;
use App\Http\Requests\CustomerLoginRequest;
use App\Services\CartService;
use App\Services\CustomerAuthService;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function __construct(
        private CustomerAuthService $auth,
        private CartService $cart,
        private WishlistService $wishlist,
    ) {}

    public function showLoginForm(): View
    {
        $banner = [
            'eyebrow' => 'BUILT FOR BRANDS',
            'headline' => 'Packaging that makes your brand stand out.',
            'description' => 'Premium custom packaging and printed essentials, crafted to elevate every brand presentation.',
            'bottom_text' => 'Custom packaging. Premium printing. Reliable delivery.',
            'image' => asset('storage/login/login-banner.png'),
        ];
        return view('login')->with([
            'banner' => $banner,
        ]);
    }

    public function login(CustomerLoginRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $customer = $this->auth->attemptLogin(
                $request->validated('email'),
                $request->validated('password'),
                $request->boolean('remember'),
                $request,
            );
        } catch (CustomerAuthException $exception) {
            return $exception->render($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Signed in successfully.',
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
            ]);
        }

        return redirect()
            ->intended(route('account'))
            ->with('success', 'Welcome back, ' . $customer->name . '.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth->logout($request);

        return redirect()
            ->route('home')
            ->with('success', 'Signed out successfully.');
    }
}
