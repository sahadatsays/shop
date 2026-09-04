<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerEmailVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        if ($customer?->hasVerifiedEmail()) {
            return redirect()->route('account');
        }

        return view('auth.verify-email', [
            'title' => 'Verify email',
        ]);
    }

    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        abort_unless($customer !== null, 403);
        abort_unless((string) $customer->getKey() === (string) $id, 403);
        abort_unless(hash_equals(sha1($customer->getEmailForVerification()), $hash), 403);

        if (! $customer->hasVerifiedEmail()) {
            $customer->markEmailAsVerified();
            event(new Verified($customer));
        }

        return redirect()
            ->route('account')
            ->with('success', 'Your email address has been verified.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        if ($customer?->hasVerifiedEmail()) {
            return redirect()->route('account');
        }

        $customer?->sendEmailVerificationNotification();

        return back()->with('success', 'A new verification link has been sent to your email address.');
    }
}
