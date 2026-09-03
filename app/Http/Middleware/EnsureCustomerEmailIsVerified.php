<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $customer = $request->user('customer');

        if (! $customer || $customer->hasVerifiedEmail()) {
            return $next($request);
        }

        return redirect()
            ->route('verification.notice')
            ->with('error', 'Please verify your email address before continuing.');
    }
}
