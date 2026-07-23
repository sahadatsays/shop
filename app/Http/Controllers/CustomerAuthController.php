<?php

namespace App\Http\Controllers;

use App\Enums\CustomerStatus;
use App\Http\Requests\CustomerLoginRequest;
use App\Models\Customer;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CustomerAuthController extends Controller
{
    public function __construct(
        private CartService $cart,
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
            ]);
        }

        return redirect()
            ->intended(route('account'))
            ->with('success', 'Welcome back, '.$customer->name.'.');
    }

    public function logout(): RedirectResponse
    {
        session()->forget('customer_id');

        return redirect()
            ->route('home')
            ->with('success', 'Signed out successfully.');
    }
}
