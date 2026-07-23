<?php

namespace App\Http\Middleware;

use App\Services\CustomerAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerAuthenticated
{
    public function __construct(private CustomerAuthService $auth) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->auth->currentCustomer()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please sign in to access your account.');
        }

        return $next($request);
    }
}
