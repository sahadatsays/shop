<?php

namespace App\Http\Middleware;

use App\Exceptions\OrderTrackingException;
use App\Models\Order;
use App\Services\OrderTrackingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrderTrackable
{
    public function __construct(private OrderTrackingService $tracking) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Order $order */
        $order = $request->route('order');

        $customer = Auth::guard('customer')->user();

        try {
            $this->tracking->authorizeView($order, $customer);
        } catch (OrderTrackingException) {
            abort(404);
        }

        return $next($request);
    }
}
