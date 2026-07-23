<?php

namespace App\Http\Controllers;

use App\Enums\CustomerStatus;
use App\Http\Requests\CustomerLoginRequest;
use App\Models\Customer;
use App\Services\AuditService;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerAuthController extends Controller
{
    public function __construct(
        private CartService $cart,
        private WishlistService $wishlist,
        private AuditService $audit,
    ) {}

    public function login(CustomerLoginRequest $request): JsonResponse|RedirectResponse
    {
        $customer = Customer::query()
            ->where('email', $request->validated('email'))
            ->where('status', CustomerStatus::Active)
            ->first();

        if (! $customer) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No active account found with that email.',
                ], 422);
            }

            return back()->withErrors(['email' => 'No active account found with that email.']);
        }

        session(['customer_id' => $customer->id]);

        $this->cart->mergeGuestIntoCustomer($customer);
        $this->wishlist->mergeGuestIntoCustomer($customer);
        $this->audit->logCustomerLogin($customer, $request);

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
            ->with('success', 'Welcome back, '.$customer->name.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $customerId = session('customer_id');
        $customer = $customerId ? Customer::query()->find($customerId) : null;

        session()->forget('customer_id');
        $this->audit->logCustomerLogout($customer, $request);

        return redirect()
            ->route('home')
            ->with('success', 'Signed out successfully.');
    }
}
