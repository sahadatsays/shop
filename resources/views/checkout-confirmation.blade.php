<x-layouts.app title="Order Confirmation" description="Your Jackpot BD LTD order has been placed successfully."
    :minimal="true">
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="text-center">
            <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-olive-100 text-olive-700">
                <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m5 13 4 4L19 7" />
                </svg>
            </span>
            <h1 class="mt-6 font-display text-3xl font-bold text-navy-900 sm:text-4xl">Order confirmed</h1>
            <p class="mt-3 text-navy-600">
                Thank you for your order. A confirmation email will be sent to
                <span class="font-medium text-navy-900">{{ $order->customer->email }}</span>.
            </p>
        </div>

        <div class="mt-10 rounded-card bg-surface p-7 shadow-card">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Order number</dt>
                    <dd class="mt-1 font-display text-lg font-bold text-navy-900">{{ $order->order_number }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Placed</dt>
                    <dd class="mt-1 text-sm font-medium text-navy-900">{{ $order->placed_at->format('M j, Y g:i A') }}
                    </dd>
                </div>
            </dl>

            <div class="mt-8 border-t border-navy-100 pt-6">
                <h2 class="font-display text-base font-bold text-navy-900">Shipping address</h2>
                @php $shipping = $order->shipping_address ?? []; @endphp
                <p class="mt-2 text-sm leading-relaxed text-navy-600">
                    {{ ($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? '') }}<br>
                    {{ $shipping['line1'] ?? '' }}@if (!empty($shipping['line2']))
                        <br>{{ $shipping['line2'] }}
                    @endif
                    <br>
                    {{ ($shipping['city'] ?? '') . ', ' . ($shipping['state'] ?? '') . ' ' . ($shipping['postal_code'] ?? '') }}<br>
                    {{ $shipping['country'] ?? '' }}
                </p>
            </div>

            <div class="mt-8 border-t border-navy-100 pt-6">
                <h2 class="font-display text-base font-bold text-navy-900">Order summary</h2>
                <ul class="mt-4 divide-y divide-navy-100">
                    @foreach ($order->items as $item)
                        <li class="flex items-center justify-between gap-4 py-3 text-sm">
                            <span class="min-w-0 truncate text-navy-800">{{ $item->product->name }} ×
                                {{ $item->quantity }}</span>
                            <span
                                class="shrink-0 font-semibold text-navy-900 tabular-nums">{{ \App\Support\MoneyFormatter::format($item->line_total_cents) }}</span>
                        </li>
                    @endforeach
                </ul>

                <dl class="mt-4 space-y-2 border-t border-navy-100 pt-4 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-navy-600">Subtotal</dt>
                        <dd class="font-medium text-navy-900 tabular-nums">{{ $formattedSubtotal }}</dd>
                    </div>
                    @if ($formattedDiscount)
                        <div class="flex justify-between">
                            <dt class="text-navy-600">Coupon{{ $order->coupon_code ? ' ('.$order->coupon_code.')' : '' }}</dt>
                            <dd class="font-medium text-green-700 tabular-nums">−{{ $formattedDiscount }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-navy-600">Shipping</dt>
                        <dd class="font-medium text-navy-900 tabular-nums">{{ $formattedShipping }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-navy-600">Tax</dt>
                        <dd class="font-medium text-navy-900 tabular-nums">{{ $formattedTax }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-navy-100 pt-3 text-base">
                        <dt class="font-display font-bold text-navy-900">Total</dt>
                        <dd class="font-display font-extrabold text-navy-900 tabular-nums">{{ $formattedTotal }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
            <x-ui.button :href="route('track-order.show', $order)" variant="primary">Track your order</x-ui.button>
            <x-ui.button :href="route('shop')" variant="secondary">Continue shopping</x-ui.button>
        </div>
    </div>

</x-layouts.app>
