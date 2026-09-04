@php
    /** @var \App\DTOs\Cart\CartSummary $summary */
    $taxRate = $taxRate ?? config('cart.tax_rate', 0);
    $currencySymbol = $currencySymbol ?? \App\Support\MoneyFormatter::symbol();

    $addressFields = function (string $prefix): array {
        $fields = [
            ['key' => 'first_name', 'label' => 'First name', 'autocomplete' => 'given-name', 'span' => false],
            ['key' => 'last_name', 'label' => 'Last name', 'autocomplete' => 'family-name', 'span' => false],
            ['key' => 'line1', 'label' => 'Street address', 'autocomplete' => 'address-line1', 'span' => true],
            [
                'key' => 'line2',
                'label' => 'Apartment, suite, unit (optional)',
                'autocomplete' => 'address-line2',
                'span' => true,
            ],
            ['key' => 'city', 'label' => 'City', 'autocomplete' => 'address-level2', 'span' => false],
            ['key' => 'state', 'label' => 'State', 'autocomplete' => 'address-level1', 'span' => false],
            ['key' => 'postal_code', 'label' => 'ZIP code', 'autocomplete' => 'postal-code', 'span' => false],
        ];

        return array_map(function (array $field) use ($prefix): array {
            $field['name'] = "{$prefix}[{$field['key']}]";
            $field['old_key'] = "{$prefix}.{$field['key']}";

            return $field;
        }, $fields);
    };
@endphp

<x-layouts.app title="Checkout" description="Complete your Jackpot BD LTD order with our fast, secure one-page checkout."
    :minimal="true">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8" data-checkout data-tax-rate="{{ $taxRate }}"
        data-currency-symbol="{{ $currencySymbol }}" data-subtotal="{{ $summary->subtotalCents / 100 }}"
        data-discount-cents="{{ $summary->discountCents }}"
        data-discount-label="{{ $summary->discountLabel() }}"
        data-coupon-code="{{ $summary->discount?->code }}">

        {{-- Progress indicator --}}
        <nav aria-label="Checkout progress" class="mx-auto max-w-xl">
            <ol class="flex items-center">
                @foreach ([['label' => 'Cart', 'state' => 'done', 'href' => route('cart')], ['label' => 'Checkout', 'state' => 'current', 'href' => null], ['label' => 'Confirmation', 'state' => 'upcoming', 'href' => null]] as $step)
                    @if (!$loop->first)
                        <li aria-hidden="true"
                            class="h-px flex-1 -translate-y-3 {{ $step['state'] === 'upcoming' ? 'bg-navy-200' : 'bg-olive-600' }}">
                        </li>
                    @endif
                    <li>
                        @if ($step['href'])
                            <a href="{{ $step['href'] }}" class="group flex flex-col items-center gap-2 px-3">
                            @else
                                <span class="flex flex-col items-center gap-2 px-3"
                                    @if ($step['state'] === 'current') aria-current="step" @endif>
                        @endif
                        <span
                            class="flex size-8 items-center justify-center rounded-full text-xs font-bold
                                {{ $step['state'] === 'done' ? 'bg-olive-600 text-white' : '' }}
                                {{ $step['state'] === 'current' ? 'bg-navy-900 text-white ring-4 ring-navy-900/10' : '' }}
                                {{ $step['state'] === 'upcoming' ? 'border border-navy-200 bg-surface text-navy-400' : '' }}">
                            @if ($step['state'] === 'done')
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                    aria-hidden="true">
                                    <path d="m5 13 4 4L19 7" />
                                </svg>
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </span>
                        <span
                            class="text-xs font-semibold {{ $step['state'] === 'upcoming' ? 'text-navy-400' : 'text-navy-900' }}">{{ $step['label'] }}</span>
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

        <form method="POST" action="{{ route('checkout.store') }}"
            class="mt-8 grid grid-cols-1 gap-10 lg:grid-cols-5">
            @csrf

            <div class="space-y-6 lg:col-span-3">

                {{-- 1. Shipping address --}}
                <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="shipping-heading">
                    <h2 id="shipping-heading"
                        class="flex items-center gap-3 font-display text-lg font-bold text-navy-900">
                        <span
                            class="flex size-8 items-center justify-center rounded-full bg-navy-900 text-sm font-bold text-white">1</span>
                        Shipping address
                    </h2>

                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <x-ui.input name="email" type="email" label="Email address" autocomplete="email"
                                placeholder="you@example.com" hint="Order updates and your receipt go here."
                                :value="old(
                                    'email',
                                    auth('customer')->user()?->email,
                                )" required />
                        </div>
                        @foreach ($addressFields('shipping') as $field)
                            <div @class(['sm:col-span-2' => $field['span']])>
                                <x-ui.input :name="$field['name']" :label="$field['label']" :autocomplete="$field['autocomplete']" :value="old($field['old_key'])"
                                    required />
                            </div>
                        @endforeach
                        <x-ui.input name="shipping[phone]" type="tel" label="Phone (optional)" autocomplete="tel"
                            hint="Only used for delivery questions." :value="old('shipping.phone')" />
                        <div class="space-y-1.5">
                            <label for="shipping-country"
                                class="block text-sm font-medium text-navy-900">Country</label>
                            <select id="shipping-country" name="shipping[country]" autocomplete="country-name" required
                                class="block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                @foreach (['Bangladesh'] as $country)
                                    <option @selected(old('shipping.country', 'Bangladesh') === $country)>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                {{-- 2. Delivery method --}}
                <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="delivery-heading">
                    <h2 id="delivery-heading"
                        class="flex items-center gap-3 font-display text-lg font-bold text-navy-900">
                        <span
                            class="flex size-8 items-center justify-center rounded-full bg-navy-900 text-sm font-bold text-white">2</span>
                        Delivery method
                    </h2>

                    <div class="mt-6 space-y-3">
                        @foreach ($shippingMethods as $index => $method)
                            <label
                                class="group flex cursor-pointer items-center gap-4 rounded-field border border-navy-200 p-5 transition-all duration-200 hover:border-navy-300 has-checked:border-navy-900 has-checked:bg-navy-50/60 has-checked:shadow-soft">
                                <input type="radio" name="delivery_method" value="{{ $method['value'] }}"
                                    data-delivery-option data-cost="{{ \App\Support\Money::toAmount((int) $method['cost_cents']) }}"
                                    @checked(old('delivery_method', $index === 0 ? $method['value'] : null) === $method['value'])
                                    class="size-4.5 border-navy-300 accent-navy-900 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                <span class="flex-1">
                                    <span
                                        class="block text-sm font-semibold text-navy-900">{{ $method['label'] }}</span>
                                    <span
                                        class="mt-0.5 block text-sm text-navy-500">{{ $method['description'] }}</span>
                                </span>
                                <span class="text-sm font-bold text-navy-900">{{ $method['price'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- 3. Payment method --}}
                <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="payment-heading">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 id="payment-heading"
                            class="flex items-center gap-3 font-display text-lg font-bold text-navy-900">
                            <span
                                class="flex size-8 items-center justify-center rounded-full bg-navy-900 text-sm font-bold text-white">3</span>
                            Payment method
                        </h2>
                        <p class="flex items-center gap-1.5 text-xs font-medium text-navy-500">
                            <svg class="size-3.5 text-olive-600" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <rect x="4" y="10" width="16" height="10" rx="2" />
                                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                            </svg>
                            256-bit SSL encrypted
                        </p>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2" role="radiogroup"
                        aria-label="Payment method">
                        <label
                            class="flex cursor-pointer items-center justify-center gap-2.5 rounded-field border border-navy-200 px-4 py-4 transition-all duration-200 hover:border-navy-300 has-checked:border-navy-900 has-checked:bg-navy-900 has-checked:text-white">
                            <input type="radio" name="payment_method" value="cod" data-payment-option
                                @checked(old('payment_method', 'cod') === 'cod') class="sr-only">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 3v18M5 8h10a3 3 0 0 1 0 6H7a3 3 0 0 0 0 6h12" />
                            </svg>
                            <span class="text-sm font-semibold">Cash on delivery</span>
                        </label>

                        <div
                            class="relative flex items-center justify-center gap-2.5 rounded-field border border-dashed border-navy-200 bg-navy-50/70 px-4 py-4 text-navy-500"
                            aria-disabled="true" title="Online payment is under construction">
                            <input type="radio" name="payment_method_disabled" value="card" disabled class="sr-only"
                                tabindex="-1">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Zm0 3h18M7 15h4" />
                            </svg>
                            <span class="text-sm font-semibold">Pay online</span>
                            <span
                                class="absolute top-2 right-2 rounded-md bg-bronze-100 px-1.5 py-0.5 text-[10px] font-bold tracking-wide text-bronze-700 uppercase">Coming
                                soon</span>
                        </div>
                    </div>

                    <div data-payment-panel="cod" class="mt-6 rounded-field bg-navy-50 p-5 text-sm text-navy-600">
                        Pay with cash when your order is delivered. Your order is reserved now; payment stays pending
                        until the courier collects it.
                    </div>

                    <div data-payment-panel="card" hidden
                        class="mt-6 rounded-field border border-bronze-200 bg-bronze-50 p-5 text-sm text-bronze-900">
                        Online card and wallet payments are under construction. Please use cash on delivery for now.
                    </div>
                </section>

                {{-- 5. Gift option --}}
                {{-- <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="gift-heading">
                    <h2 id="gift-heading" class="sr-only">Gift option</h2>
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" data-gift-toggle
                            class="mt-0.5 size-4.5 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                        <span>
                            <span class="flex items-center gap-2 text-sm font-semibold text-navy-900">
                                <svg class="size-4.5 text-bronze-600" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="8" width="18" height="4" />
                                    <path
                                        d="M5 12v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-8M12 8v13M12 8s-1.5-4-4-4a2 2 0 0 0 0 4h4Zm0 0s1.5-4 4-4a2 2 0 0 1 0 4h-4Z" />
                                </svg>
                                This order is a gift
                            </span>
                            <span class="mt-1 block text-sm text-navy-500">Free gift wrap, a handwritten note, and
                                prices hidden on the packing slip.</span>
                        </span>
                    </label>

                    <div data-gift-fields hidden class="mt-5 space-y-1.5">
                        <label for="gift-message" class="block text-sm font-medium text-navy-900">Gift message
                            (optional)</label>
                        <textarea id="gift-message" name="gift-message" rows="3" maxlength="200"
                            placeholder="Thank you for your service…"
                            class="block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink placeholder:text-navy-400 shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500"></textarea>
                        <p class="text-sm text-navy-500">Handwritten on a Jackpot BD LTD note card, up to 200
                            characters.</p>
                    </div>
                </section> --}}
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
                                        <img src="{{ $line->imageUrl() }}" alt="{{ $product->name }}"
                                            loading="lazy" class="size-full object-cover">
                                    @else
                                        <div
                                            class="flex size-full items-center justify-center bg-linear-to-br from-navy-100 to-bronze-100">
                                            <svg class="size-6 text-navy-300" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                                <rect x="3" y="3" width="18" height="18" rx="3" />
                                            </svg>
                                        </div>
                                    @endif
                                    <span
                                        class="absolute top-0 right-0 flex size-5 items-center justify-center rounded-bl-xl bg-navy-900 text-[0.65rem] font-bold text-white"
                                        aria-hidden="true">{{ $item->quantity }}</span>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span
                                        class="block truncate text-sm font-semibold text-navy-900">{{ $product->name }}</span>
                                    <span class="mt-0.5 block text-xs text-navy-500">{{ $product->category?->name }} ·
                                        Qty {{ $item->quantity }}</span>
                                </span>
                                <span class="text-sm font-bold text-navy-900 tabular-nums" data-item-total
                                    data-price="{{ $item->unit_price_cents / 100 }}"
                                    data-qty="{{ $item->quantity }}">
                                    {{ $line->formattedLineTotal() }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Promo code --}}
                    <div class="mt-6 border-t border-navy-100 pt-6" data-promo-section>
                        <div class="flex gap-3" data-promo-form @if ($summary->hasDiscount()) hidden @endif>
                            <label for="promo-code" class="sr-only">Promo code</label>
                            <input type="text" id="promo-code" data-promo-input
                                placeholder="Promo code"
                                class="w-full rounded-field border border-navy-200 bg-canvas px-4 py-2.5 text-sm text-ink uppercase placeholder:normal-case placeholder:text-navy-400 transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                            <x-ui.button type="button" variant="outline" size="sm" class="shrink-0"
                                data-promo-apply>Apply</x-ui.button>
                        </div>
                        <p data-promo-error hidden class="mt-2 text-sm font-medium text-red-600"></p>
                        <p data-promo-applied @if (! $summary->hasDiscount()) hidden @endif
                            class="mt-2 flex items-center gap-1.5 text-sm font-semibold text-green-700">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m5 13 4 4L19 7" />
                            </svg>
                            <span data-promo-applied-label>{{ $summary->discountLabel() }}</span>
                            <button type="button" data-promo-remove
                                class="ml-auto font-medium text-navy-500 underline-offset-4 hover:underline">Remove</button>
                        </p>
                    </div>

                    {{-- Totals --}}
                    <dl class="mt-6 space-y-3 border-t border-navy-100 pt-6 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-navy-600">Subtotal</dt>
                            <dd class="font-semibold text-navy-900 tabular-nums" data-total-subtotal>
                                {{ $summary->formattedSubtotal() }}</dd>
                        </div>
                        <div class="flex items-center justify-between" data-total-discount-row @if (! $summary->hasDiscount()) hidden @endif>
                            <dt class="text-navy-600" data-total-discount-label>Coupon{{ $summary->discount?->code ? ' ('.$summary->discount->code.')' : '' }}</dt>
                            <dd class="font-semibold text-green-700 tabular-nums" data-total-discount>
                                {{ $summary->hasDiscount() ? $summary->formattedDiscount() : '' }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-navy-600">Shipping</dt>
                            <dd class="font-semibold text-navy-900 tabular-nums" data-total-shipping>
                                {{ $summary->formattedShipping() }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-navy-600">Estimated tax ({{ number_format($taxRate * 100, 0) }}%)</dt>
                            <dd class="font-semibold text-navy-900 tabular-nums" data-total-tax>
                                {{ $summary->formattedTax() }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-navy-100 pt-3 text-base">
                            <dt class="font-display font-bold text-navy-900">Total</dt>
                            <dd class="font-display text-xl font-extrabold text-navy-900 tabular-nums"
                                data-total-grand>{{ $summary->formattedTotal() }}</dd>
                        </div>
                    </dl>

                    @if ($errors->any())
                        <div class="mt-6 rounded-field border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Terms + place order --}}
                    <label class="mt-6 flex cursor-pointer items-start gap-3">
                        <input type="checkbox" name="terms_accepted" value="1" data-terms-checkbox
                            @checked(old('terms_accepted'))
                            class="mt-0.5 size-4.5 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                        <span class="text-sm text-navy-600">
                            I agree to the <a href="#"
                                class="font-medium text-navy-900 underline underline-offset-4 hover:text-olive-700">Terms
                                of Service</a>,
                            <a href="#"
                                class="font-medium text-navy-900 underline underline-offset-4 hover:text-olive-700">Privacy
                                Policy</a>, and
                            <a href="#"
                                class="font-medium text-navy-900 underline underline-offset-4 hover:text-olive-700">Refund
                                Policy</a>.
                        </span>
                    </label>

                    @if (old('terms_accepted'))
                        <x-ui.button type="submit" variant="accent" size="lg" class="mt-5 w-full"
                            data-place-order>
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <rect x="4" y="10" width="16" height="10" rx="2" />
                                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                            </svg>
                            <span data-place-order-label>Place order · {{ $summary->formattedTotal() }}</span>
                        </x-ui.button>
                    @else
                        <x-ui.button type="submit" variant="accent" size="lg" class="mt-5 w-full"
                            data-place-order disabled>
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <rect x="4" y="10" width="16" height="10" rx="2" />
                                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                            </svg>
                            <span data-place-order-label>Place order · {{ $summary->formattedTotal() }}</span>
                        </x-ui.button>
                    @endif
                    <p class="mt-3 text-center text-xs text-navy-500">You won't be charged until you review and
                        confirm.</p>

                    {{-- <ul class="mt-6 grid grid-cols-3 gap-2 border-t border-navy-100 pt-6 text-center">
                        @foreach ([['label' => 'SSL secured', 'icon' => 'M4 10h16v10H4zM8 10V7a4 4 0 0 1 8 0v3'], ['label' => '30-day returns', 'icon' => 'M3 12a9 9 0 1 0 3-6.7M3 4v4h4'], ['label' => 'Lifetime warranty', 'icon' => 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z']] as $badge)
                            <li class="flex flex-col items-center gap-1.5 text-xs font-medium text-navy-600">
                                <svg class="size-5 text-olive-600" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path d="{{ $badge['icon'] }}" />
                                </svg>
                                {{ $badge['label'] }}
                            </li>
                        @endforeach
                    </ul> --}}
                </div>
            </aside>
        </form>
    </div>
</x-layouts.app>
