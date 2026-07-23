<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerAuthenticated
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('customer_id')) {
            return redirect()
                ->route('login')
                ->with('error', 'Please sign in to access your account notifications.');
        }

        return $next($request);
    }
}
