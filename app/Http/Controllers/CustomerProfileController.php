<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomerAuthException;
use App\Http\Requests\UpdateCustomerProfileRequest;
use App\Services\CustomerAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerProfileController extends Controller
{
    public function __construct(private CustomerAuthService $auth) {}

    public function show(Request $request): View|RedirectResponse
    {
        $customer = $this->auth->currentCustomer();

        if (! $customer) {
            return redirect()->route('login');
        }

        $nameParts = preg_split('/\s+/', trim($customer->name), 2) ?: [];

        return view('account-settings', [
            'customer' => $customer,
            'firstName' => $nameParts[0] ?? '',
            'lastName' => $nameParts[1] ?? '',
        ]);
    }

    public function update(UpdateCustomerProfileRequest $request): JsonResponse|RedirectResponse
    {
        $customer = $this->auth->currentCustomer();

        if (! $customer) {
            return redirect()->route('login');
        }

        $data = $request->validated();

        try {
            $customer = $this->auth->updateProfile($customer, $data, $request);
        } catch (CustomerAuthException $exception) {
            return $exception->render($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your profile has been updated successfully.',
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'avatar_url' => $customer->avatarUrl(),
                    'initials' => $customer->initials(),
                ],
            ]);
        }

        return back()->with('success', 'Your profile has been updated successfully.');
    }

    public function addresses(): View|RedirectResponse
    {
        $customer = $this->auth->currentCustomer();

        if (! $customer) {
            return redirect()->route('login');
        }

        return view('account-addresses', [
            'customer' => $customer,
            'addresses' => $customer->addresses()->orderByDesc('is_default')->orderBy('id')->get(),
        ]);
    }
}
