@php
    /** @var \App\DTOs\Cart\CartSummary $summary */
    $taxRate = config('cart.tax_rate', 0.08);

    $addressFields = function (string $prefix): array {
        return [
            ['name' => $prefix.'-first-name', 'label' => 'First name', 'autocomplete' => 'given-name', 'span' => false],
            ['name' => $prefix.'-last-name', 'label' => 'Last name', 'autocomplete' => 'family-name', 'span' => false],
            ['name' => $prefix.'-address', 'label' => 'Street address', 'autocomplete' => 'address-line1', 'span' => true],
            ['name' => $prefix.'-address-2', 'label' => 'Apartment, suite, unit (optional)', 'autocomplete' => 'address-line2', 'span' => true],
            ['name' => $prefix.'-city', 'label' => 'City', 'autocomplete' => 'address-level2', 'span' => false],
            ['name' => $prefix.'-state', 'label' => 'State', 'autocomplete' => 'address-level1', 'span' => false],
            ['name' => $prefix.'-zip', 'label' => 'ZIP code', 'autocomplete' => 'postal-code', 'span' => false],
        ];
    };
@endphp

<x-layouts.app title="Checkout" description="Complete your Valor Supply Co. order with our fast, secure one-page checkout." :minimal="true">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8" data-checkout data-tax-rate="0.08">

        {{-- Progress indicator --}}
        <nav aria-label="Checkout progress" class="mx-auto max-w-xl">
            <ol class="flex items-center">
                @foreach ([
                    ['label' => 'Cart', 'state' => 'done', 'href' => route('cart')],
                    ['label' => 'Checkout', 'state' => 'current', 'href' => null],
                    ['label' => 'Confirmation', 'state' => 'upcoming', 'href' => null],
                ] as $step)
                    @if (!$loop->first)
                        <li aria-hidden="true" class="h-px flex-1 -translate-y-3 {{ $step['state'] === 'upcoming' ? 'bg-navy-200' : 'bg-olive-600' }}"></li>
                    @endif
                    <li>
                        @if ($step['href'])
                            <a href="{{ $step['href'] }}" class="group flex flex-col items-center gap-2 px-3">
                        @else
                            <span class="flex flex-col items-center gap-2 px-3" @if ($step['state'] === 'current') aria-current="step" @endif>
                        @endif
                            <span class="flex size-8 items-center justify-center rounded-full text-xs font-bold
                                {{ $step['state'] === 'done' ? 'bg-olive-600 text-white' : '' }}
                                {{ $step['state'] === 'current' ? 'bg-navy-900 text-white ring-4 ring-navy-900/10' : '' }}
                                {{ $step['state'] === 'upcoming' ? 'border border-navy-200 bg-surface text-navy-400' : '' }}">
                                @if ($step['state'] === 'done')
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </span>
                            <span class="text-xs font-semibold {{ $step['state'] === 'upcoming' ? 'text-navy-400' : 'text-navy-900' }}">{{ $step['label'] }}</span>
                        @if ($step['href'])
                            </a>
                        @else
                            </span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        <h1 class="mt-10 font-display text-3xl font-bold text-navy-900 sm:text-4xl">Checkout</h1>
        <p class="mt-2 text-navy-600">Almost there. Complete the details below to place your order.</p>

        <form class="mt-8 grid grid-cols-1 gap-10 lg:grid-cols-5" novalidate>

            <div class="space-y-6 lg:col-span-3">

                {{-- 1. Shipping address --}}
                <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="shipping-heading">
                    <h2 id="shipping-heading" class="flex items-center gap-3 font-display text-lg font-bold text-navy-900">
                        <span class="flex size-8 items-center justify-center rounded-full bg-navy-900 text-sm font-bold text-white">1</span>
                        Shipping address
                    </h2>

                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <x-ui.input name="email" type="email" label="Email address" autocomplete="email" placeholder="you@example.com" hint="Order updates and your receipt go here." />
                        </div>
                        @foreach ($addressFields('shipping') as $field)
                            <div @class(['sm:col-span-2' => $field['span']])>
                                <x-ui.input :name="$field['name']" :label="$field['label']" :autocomplete="$field['autocomplete']" />
                            </div>
                        @endforeach
                        <x-ui.input name="shipping-phone" type="tel" label="Phone (optional)" autocomplete="tel" hint="Only used for delivery questions." />
                        <div class="space-y-1.5">
                            <label for="shipping-country" class="block text-sm font-medium text-navy-900">Country</label>
                            <select id="shipping-country" name="shipping-country" autocomplete="country-name"
                                    class="block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                <option>United States</option>
                                <option>Canada</option>
                                <option>United Kingdom</option>
                                <option>Australia</option>
                            </select>
                        </div>
                    </div>
                </section>

                {{-- 2. Billing address --}}
                <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="billing-heading">
                    <h2 id="billing-heading" class="flex items-center gap-3 font-display text-lg font-bold text-navy-900">
                        <span class="flex size-8 items-center justify-center rounded-full bg-navy-900 text-sm font-bold text-white">2</span>
                        Billing address
                    </h2>

                    <label class="mt-6 flex cursor-pointer items-center gap-3">
                        <input type="checkbox" data-billing-same checked
                               class="size-4.5 rounded border-navy-300 text-olive-600 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                        <span class="text-sm font-medium text-navy-800">Same as shipping address</span>
                    </label>

                    <div data-billing-fields hidden class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($addressFields('billing') as $field)
                            <div @class(['sm:col-span-2' => $field['span']])>
                                <x-ui.input :name="$field['name']" :label="$field['label']" :autocomplete="'billing '.$field['autocomplete']" />
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- 3. Delivery method --}}
                <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="delivery-heading">
                    <h2 id="delivery-heading" class="flex items-center gap-3 font-display text-lg font-bold text-navy-900">
                        <span class="flex size-8 items-center justify-center rounded-full bg-navy-900 text-sm font-bold text-white">3</span>
                        Delivery method
                    </h2>

                    <div class="mt-6 space-y-3">
                        @foreach ([
                            ['value' => 'standard', 'cost' => 0, 'label' => 'Standard shipping', 'eta' => 'Arrives in 5–7 business days', 'price' => 'Free', 'checked' => true],
                            ['value' => 'express', 'cost' => 12, 'label' => 'Express shipping', 'eta' => 'Arrives in 2–3 business days', 'price' => '$12.00', 'checked' => false],
                            ['value' => 'overnight', 'cost' => 24, 'label' => 'Overnight shipping', 'eta' => 'Next business day by 5 PM', 'price' => '$24.00', 'checked' => false],
                        ] as $method)
                            <label class="group flex cursor-pointer items-center gap-4 rounded-field border border-navy-200 p-5 transition-all duration-200 hover:border-navy-300 has-checked:border-navy-900 has-checked:bg-navy-50/60 has-checked:shadow-soft">
                                <input type="radio" name="delivery-method" value="{{ $method['value'] }}" data-delivery-option data-cost="{{ $method['cost'] }}"
                                       @checked($method['checked'])
                                       class="size-4.5 border-navy-300 accent-navy-900 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                <span class="flex-1">
                                    <span class="block text-sm font-semibold text-navy-900">{{ $method['label'] }}</span>
                                    <span class="mt-0.5 block text-sm text-navy-500">{{ $method['eta'] }}</span>
                                </span>
                                <span class="text-sm font-bold text-navy-900">{{ $method['price'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- 4. Payment method --}}
                <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="payment-heading">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 id="payment-heading" class="flex items-center gap-3 font-display text-lg font-bold text-navy-900">
                            <span class="flex size-8 items-center justify-center rounded-full bg-navy-900 text-sm font-bold text-white">4</span>
                            Payment method
                        </h2>
                        <p class="flex items-center gap-1.5 text-xs font-medium text-navy-500">
                            <svg class="size-3.5 text-olive-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                            </svg>
                            256-bit SSL encrypted
                        </p>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3" role="radiogroup" aria-label="Payment method">
                        @foreach ([
                            ['value' => 'card', 'label' => 'Card', 'checked' => true, 'icon' => 'M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Zm0 3h18M7 15h4'],
                            ['value' => 'paypal', 'label' => 'PayPal', 'checked' => false, 'icon' => 'M7 21l1-6h3.5c3.5 0 6-2 6.5-5.5C18.4 6 16.5 4 13 4H8L5.5 18H9'],
                            ['value' => 'applepay', 'label' => 'Apple Pay', 'checked' => false, 'icon' => 'M16 5c-.8 1-2 1.7-3.1 1.6-.2-1.2.4-2.5 1-3.3.8-1 2.1-1.7 3.2-1.7.1 1.2-.3 2.4-1.1 3.4Zm1 2.2c-1.7-.1-3.2 1-4 1-.9 0-2.1-1-3.5-.9-1.8 0-3.5 1-4.4 2.7-1.9 3.3-.5 8.1 1.3 10.8.9 1.3 2 2.8 3.4 2.7 1.3-.1 1.9-.9 3.5-.9s2.1.9 3.5.9c1.5 0 2.4-1.3 3.3-2.6.7-1 1.3-2.2 1.6-3.4-2.2-.9-3.2-3.3-2.6-5.4.4-1.5 1.4-2.6 2.4-3.2-1-1.4-2.5-1.7-3.5-1.7Z'],
                        ] as $payment)
                            <label class="flex cursor-pointer items-center justify-center gap-2.5 rounded-field border border-navy-200 px-4 py-4 transition-all duration-200 hover:border-navy-300 has-checked:border-navy-900 has-checked:bg-navy-900 has-checked:text-white">
                                <input type="radio" name="payment-method" value="{{ $payment['value'] }}" data-payment-option @checked($payment['checked']) class="sr-only">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $payment['icon'] }}"/></svg>
                                <span class="text-sm font-semibold">{{ $payment['label'] }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div data-payment-panel="card" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <x-ui.input name="card-number" label="Card number" inputmode="numeric" autocomplete="cc-number" placeholder="1234 5678 9012 3456" data-card-number maxlength="19" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-ui.input name="card-name" label="Name on card" autocomplete="cc-name" />
                        </div>
                        <x-ui.input name="card-expiry" label="Expiry date" inputmode="numeric" autocomplete="cc-exp" placeholder="MM / YY" data-card-expiry maxlength="7" />
                        <x-ui.input name="card-cvc" label="Security code" inputmode="numeric" autocomplete="cc-csc" placeholder="CVC" maxlength="4" hint="3–4 digits on the back of your card." />
                    </div>

                    <div data-payment-panel="paypal" hidden class="mt-6 rounded-field bg-navy-50 p-5 text-sm text-navy-600">
                        You'll be redirected to PayPal to complete your purchase securely. Your order total will be confirmed before payment.
                    </div>

                    <div data-payment-panel="applepay" hidden class="mt-6 rounded-field bg-navy-50 p-5 text-sm text-navy-600">
                        Confirm your order with Face ID or Touch ID when you press the Apple Pay button. Your card details never leave your device.
                    </div>
                </section>

                {{-- 5. Gift option --}}
                <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="gift-heading">
                    <h2 id="gift-heading" class="sr-only">Gift option</h2>
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" data-gift-toggle
                               class="mt-0.5 size-4.5 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                        <span>
                            <span class="flex items-center gap-2 text-sm font-semibold text-navy-900">
                                <svg class="size-4.5 text-bronze-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="8" width="18" height="4"/><path d="M5 12v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-8M12 8v13M12 8s-1.5-4-4-4a2 2 0 0 0 0 4h4Zm0 0s1.5-4 4-4a2 2 0 0 1 0 4h-4Z"/>
                                </svg>
                                This order is a gift
                            </span>
                            <span class="mt-1 block text-sm text-navy-500">Free gift wrap, a handwritten note, and prices hidden on the packing slip.</span>
                        </span>
                    </label>

                    <div data-gift-fields hidden class="mt-5 space-y-1.5">
                        <label for="gift-message" class="block text-sm font-medium text-navy-900">Gift message (optional)</label>
                        <textarea id="gift-message" name="gift-message" rows="3" maxlength="200" placeholder="Thank you for your service…"
                                  class="block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink placeholder:text-navy-400 shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500"></textarea>
                        <p class="text-sm text-navy-500">Handwritten on a Valor Supply Co. note card, up to 200 characters.</p>
                    </div>
                </section>
            </div>

            {{-- ============ Order summary ============ --}}
            <aside class="lg:col-span-2 lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-card bg-surface p-7 shadow-card">
                    <h2 class="font-display text-lg font-bold text-navy-900">Order summary</h2>

                    <ul class="mt-6 space-y-4">
                        @foreach ($summary->items as $line)
                            @php
                                $item = $line->cartItem;
                                $product = $line->product;
                            @endphp
                            <li class="flex items-center gap-4">
                                <span class="relative block size-16 shrink-0 overflow-hidden rounded-xl bg-navy-100">
                                    @if ($line->imageUrl())
                                        <img src="{{ $line->imageUrl() }}" alt="{{ $product->name }}" loading="lazy" class="size-full object-cover">
                                    @else
                                        <div class="flex size-full items-center justify-center bg-linear-to-br from-navy-100 to-bronze-100">
                                            <svg class="size-6 text-navy-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                                <rect x="3" y="3" width="18" height="18" rx="3"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="absolute top-0 right-0 flex size-5 items-center justify-center rounded-bl-xl bg-navy-900 text-[0.65rem] font-bold text-white" aria-hidden="true">{{ $item->quantity }}</span>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-navy-900">{{ $product->name }}</span>
                                    <span class="mt-0.5 block text-xs text-navy-500">{{ $product->category?->name }} · Qty {{ $item->quantity }}</span>
                                </span>
                                <span class="text-sm font-bold text-navy-900 tabular-nums" data-item-total data-price="{{ $item->unit_price_cents / 100 }}" data-qty="{{ $item->quantity }}">
                                    {{ $line->formattedLineTotal() }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Promo code --}}
                    <div class="mt-6 border-t border-navy-100 pt-6">
                        <div class="flex gap-3">
                            <label for="promo-code" class="sr-only">Promo code</label>
                            <input type="text" id="promo-code" data-promo-input placeholder="Promo code — try VALOR10"
                                   class="w-full rounded-field border border-navy-200 bg-canvas px-4 py-2.5 text-sm text-ink uppercase placeholder:normal-case placeholder:text-navy-400 transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                            <x-ui.button type="button" variant="outline" size="sm" class="shrink-0" data-promo-apply>Apply</x-ui.button>
                        </div>
                        <p data-promo-error hidden class="mt-2 text-sm font-medium text-red-600">That code isn't valid. Try VALOR10.</p>
                        <p data-promo-applied hidden class="mt-2 flex items-center gap-1.5 text-sm font-semibold text-green-700">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                            VALOR10 applied — 10% off
                            <button type="button" data-promo-remove class="ml-auto font-medium text-navy-500 underline-offset-4 hover:underline">Remove</button>
                        </p>
                    </div>

                    {{-- Totals --}}
                    <dl class="mt-6 space-y-3 border-t border-navy-100 pt-6 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-navy-600">Subtotal</dt>
                            <dd class="font-semibold text-navy-900 tabular-nums" data-total-subtotal>$530.00</dd>
                        </div>
                        <div class="flex items-center justify-between" data-total-discount-row hidden>
                            <dt class="text-navy-600">Promo discount (10%)</dt>
                            <dd class="font-semibold text-green-700 tabular-nums" data-total-discount>−$0.00</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-navy-600">Shipping</dt>
                            <dd class="font-semibold text-navy-900 tabular-nums" data-total-shipping>Free</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-navy-600">Estimated tax (8%)</dt>
                            <dd class="font-semibold text-navy-900 tabular-nums" data-total-tax>$42.40</dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-navy-100 pt-3 text-base">
                            <dt class="font-display font-bold text-navy-900">Total</dt>
                            <dd class="font-display text-xl font-extrabold text-navy-900 tabular-nums" data-total-grand>$572.40</dd>
                        </div>
                    </dl>

                    {{-- Terms + place order --}}
                    <label class="mt-6 flex cursor-pointer items-start gap-3">
                        <input type="checkbox" data-terms-checkbox
                               class="mt-0.5 size-4.5 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                        <span class="text-sm text-navy-600">
                            I agree to the <a href="#" class="font-medium text-navy-900 underline underline-offset-4 hover:text-olive-700">Terms of Service</a>,
                            <a href="#" class="font-medium text-navy-900 underline underline-offset-4 hover:text-olive-700">Privacy Policy</a>, and
                            <a href="#" class="font-medium text-navy-900 underline underline-offset-4 hover:text-olive-700">Refund Policy</a>.
                        </span>
                    </label>

                    <x-ui.button type="submit" variant="accent" size="lg" class="mt-5 w-full" data-place-order disabled>
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                        </svg>
                        <span data-place-order-label>Pay $572.40 securely</span>
                    </x-ui.button>
                    <p class="mt-3 text-center text-xs text-navy-500">You won't be charged until you review and confirm.</p>

                    <ul class="mt-6 grid grid-cols-3 gap-2 border-t border-navy-100 pt-6 text-center">
                        @foreach ([
                            ['label' => 'SSL secured', 'icon' => 'M4 10h16v10H4zM8 10V7a4 4 0 0 1 8 0v3'],
                            ['label' => '30-day returns', 'icon' => 'M3 12a9 9 0 1 0 3-6.7M3 4v4h4'],
                            ['label' => 'Lifetime warranty', 'icon' => 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z'],
                        ] as $badge)
                            <li class="flex flex-col items-center gap-1.5 text-xs font-medium text-navy-600">
                                <svg class="size-5 text-olive-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $badge['icon'] }}"/></svg>
                                {{ $badge['label'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </form>
    </div>
</x-layouts.app>
