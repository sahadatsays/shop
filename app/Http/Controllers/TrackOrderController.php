<?php

namespace App\Http\Controllers;

use App\Exceptions\OrderTrackingException;
use App\Http\Requests\TrackOrderRequest;
use App\Http\Resources\OrderTrackingResource;
use App\Models\Order;
use App\Services\OrderTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrackOrderController extends Controller
{
    public function __construct(private OrderTrackingService $tracking) {}

    public function create(): View
    {
        return view('track-order', [
            'title' => 'Track Order',
        ]);
    }

    public function store(TrackOrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->tracking->lookup(
                $request->string('order_number')->toString(),
                $request->string('email')->toString(),
            );
        } catch (OrderTrackingException) {
            return back()
                ->withInput($request->only('order_number', 'email'))
                ->withErrors(['order_number' => OrderTrackingException::invalidCredentials()->getMessage()]);
        }

        return redirect()->route('track-order.show', $order);
    }

    public function show(Order $order): View
    {
        $order = $this->tracking->show($order);

        return view('track', [
            'title' => 'Track Shipment',
            'tracking' => OrderTrackingResource::make($order)->resolve(),
        ]);
    }
}
