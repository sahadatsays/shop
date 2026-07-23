<?php

namespace App\Http\Middleware;

use App\Exceptions\OrderTrackingException;
use App\Models\Customer;
use App\Models\Order;
use App\Services\OrderTrackingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrderTrackable
{
    public function __construct(private OrderTrackingService $tracking) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Order $order */
        $order = $request->route('order');

        $customerId = session('customer_id');
        $customer = $customerId ? Customer::query()->find($customerId) : null;

        try {
            $this->tracking->authorizeView($order, $customer);
        } catch (OrderTrackingException) {
            abort(403, 'You do not have permission to view this order.');
        }

        return $next($request);
    }
}
