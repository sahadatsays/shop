@php
    /** @var array<string, mixed> $order */
    $timeline = $order['timeline'];
    $items = $order['items'];
    $reviewableProductIds = $reviewableProductIds ?? [];
@endphp

<x-layouts.app :title="$title" description="Order details for {{ $order['order_number_display'] }} — items, shipment progress, and delivery information.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-track>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">

            <x-account.sidebar active="Orders" />

            <div class="space-y-8 lg:col-span-3">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                        <li><a href="{{ route('account') }}" class="transition-colors duration-200 hover:text-navy-900">Account</a></li>
                        <li aria-hidden="true">/</li>
                        <li><a href="{{ route('account.orders') }}" class="transition-colors duration-200 hover:text-navy-900">Orders</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="font-medium text-navy-900">{{ $order['order_number_display'] }}</li>
                    </ol>
                </nav>

                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">Order details</h1>
                        <p class="mt-2 text-navy-600">Placed {{ $order['placed_at'] }} · {{ $order['item_count'] }} {{ $order['item_count'] === 1 ? 'item' : 'items' }}</p>
                    </div>
                    <x-ui.badge :variant="$order['status_variant']" class="text-sm">{{ $order['status'] }}</x-ui.badge>
                </div>

                {{-- Status banner --}}
                <section class="relative overflow-hidden rounded-card bg-navy-900 p-7 text-white sm:p-8">
                    <div class="absolute -top-20 -right-16 size-56 rounded-full bg-olive-600/30 blur-3xl" aria-hidden="true"></div>
                    <div class="relative flex flex-wrap items-end justify-between gap-6">
                        <div>
                            <p class="text-sm font-medium text-navy-200">{{ $order['eta_heading'] }}</p>
                            <p class="mt-2 font-display text-2xl font-extrabold sm:text-3xl">{{ $order['eta_detail'] }}</p>
                            <p class="mt-2 text-sm text-navy-200">{{ $order['payment_status'] }} via {{ $order['payment_method'] }}</p>
                        </div>
                        <div class="w-full max-w-xs">
                            <div class="flex items-center justify-between text-xs font-medium text-navy-200">
                                <span>{{ $order['progress_step'] }} of {{ $order['progress_total'] }} steps</span>
                                <span>{{ $order['progress_percent'] }}%</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/20">
                                <div class="h-full rounded-full bg-bronze-400" style="width: {{ $order['progress_percent'] }}%" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Milestone strip --}}
                <nav aria-label="Order milestones" class="overflow-x-auto rounded-card bg-surface p-5 shadow-soft scrollbar-none">
                    <ol class="flex min-w-max items-center gap-0 px-2 sm:min-w-0 sm:justify-between">
                        @foreach ($timeline as $step)
                            @if (! $loop->first)
                                <li aria-hidden="true" class="h-px w-6 shrink-0 -translate-y-3 sm:w-auto sm:flex-1 {{ $timeline[$loop->index - 1]['state'] === 'done' ? 'bg-olive-600' : 'bg-navy-200' }}"></li>
                            @endif
                            <li class="flex flex-col items-center gap-2 px-1">
                                <span @class([
                                    'flex size-7 items-center justify-center rounded-full sm:size-8',
                                    'bg-olive-600 text-white' => $step['state'] === 'done',
                                    'bg-navy-900 text-white ring-4 ring-navy-900/10' => $step['state'] === 'current',
                                    'border border-navy-200 bg-surface' => $step['state'] === 'upcoming',
                                ])>
                                    @if ($step['state'] === 'done')
                                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                    @elseif ($step['state'] === 'current')
                                        <span class="size-1.5 animate-ping rounded-full bg-bronze-400" aria-hidden="true"></span>
                                    @else
                                        <span class="size-1.5 rounded-full bg-navy-300" aria-hidden="true"></span>
                                    @endif
                                </span>
                                <span @class([
                                    'max-w-16 text-center text-[0.65rem] font-semibold leading-tight sm:max-w-none sm:text-xs',
                                    'text-navy-400' => $step['state'] === 'upcoming',
                                    'text-navy-900' => $step['state'] !== 'upcoming',
                                ])>{{ $step['label'] }}</span>
                            </li>
                        @endforeach
                    </ol>
                </nav>

                <div class="grid grid-cols-1 gap-8 xl:grid-cols-5">
                    {{-- Timeline --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft xl:col-span-2" aria-labelledby="timeline-heading">
                        <h2 id="timeline-heading" class="font-display text-lg font-bold text-navy-900">Shipment progress</h2>
                        <ol class="mt-6">
                            @foreach ($timeline as $step)
                                <li class="relative flex gap-4 {{ $loop->last ? '' : 'pb-8' }}">
                                    @unless ($loop->last)
                                        <span class="absolute top-8 left-3.75 h-full w-0.5 {{ $step['state'] === 'done' ? 'bg-olive-600' : 'bg-navy-200' }}" aria-hidden="true"></span>
                                    @endunless
                                    <span @class([
                                        'relative flex size-8 shrink-0 items-center justify-center rounded-full',
                                        'bg-olive-600 text-white' => $step['state'] === 'done',
                                        'bg-navy-900 text-white ring-4 ring-navy-900/10' => $step['state'] === 'current',
                                        'border border-navy-200 bg-surface text-navy-400' => $step['state'] === 'upcoming',
                                    ])>
                                        @if ($step['state'] === 'done')
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                        @elseif ($step['state'] === 'current')
                                            <span class="size-2 animate-ping rounded-full bg-bronze-400" aria-hidden="true"></span>
                                        @else
                                            <span class="size-2 rounded-full bg-navy-300" aria-hidden="true"></span>
                                        @endif
                                    </span>
                                    <div class="pt-1">
                                        <p @class([
                                            'text-sm font-semibold',
                                            'text-navy-400' => $step['state'] === 'upcoming',
                                            'text-navy-900' => $step['state'] !== 'upcoming',
                                        ])>
                                            {{ $step['label'] }}
                                            @if ($step['state'] === 'current')
                                                <x-ui.badge variant="bronze" class="ml-2">Now</x-ui.badge>
                                            @endif
                                        </p>
                                        <p class="mt-0.5 text-xs text-navy-500">{{ $step['meta'] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>

                        @if ($order['courier_name'] || $order['tracking_number_display'])
                            <div class="mt-6 border-t border-navy-100 pt-6">
                                <h3 class="text-sm font-semibold text-navy-900">Courier &amp; tracking</h3>
                                <dl class="mt-3 space-y-3 text-sm">
                                    @if ($order['courier_name'])
                                        <div>
                                            <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Carrier</dt>
                                            <dd class="mt-1 text-navy-700">{{ $order['courier_name'] }}</dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Tracking number</dt>
                                        <dd class="mt-1 flex items-center gap-2">
                                            @if ($order['tracking_number_display'])
                                                <code class="rounded-lg bg-navy-50 px-2.5 py-1 font-mono text-xs font-semibold text-navy-800" data-tracking-number>{{ $order['tracking_number_display'] }}</code>
                                                <button type="button" data-copy-tracking aria-label="Copy tracking number"
                                                        class="flex size-8 items-center justify-center rounded-lg text-navy-500 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-900">
                                                    <svg data-copy-icon class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                    </svg>
                                                    <svg data-copied-icon hidden class="size-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                                </button>
                                            @else
                                                <span class="text-navy-500">Available once your order ships.</span>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        @endif
                    </section>

                    <div class="space-y-8 xl:col-span-3">
                        {{-- Items --}}
                        <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="items-heading">
                            <h2 id="items-heading" class="font-display text-lg font-bold text-navy-900">Items in this order</h2>
                            <ul class="mt-5 divide-y divide-navy-100">
                                @foreach ($items as $item)
                                    <li class="flex flex-wrap items-center gap-4 py-5 first:pt-0 last:pb-0">
                                        <a href="{{ $item['url'] }}" class="block size-16 shrink-0 overflow-hidden rounded-xl bg-navy-100 ring-1 ring-navy-900/5">
                                            @if ($item['image'])
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy" class="size-full object-cover">
                                            @endif
                                        </a>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ $item['url'] }}" class="font-display text-base font-bold text-navy-900 transition-colors duration-200 hover:text-olive-700">{{ $item['name'] }}</a>
                                            @if ($item['sku'])
                                                <p class="mt-0.5 text-xs text-navy-500">SKU: {{ $item['sku'] }}</p>
                                            @endif
                                            <p class="mt-1 text-sm text-navy-600">Qty {{ $item['quantity'] }} · {{ $item['unit_price'] }} each</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-navy-900 tabular-nums">{{ $item['subtotal'] }}</p>
                                            @if ($order['is_delivered'] && in_array($item['product_id'], $reviewableProductIds, true))
                                                <x-ui.button :href="route('account.reviews').'#ready-for-review'" size="sm" variant="outline" class="mt-2">
                                                    Write review
                                                </x-ui.button>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </section>

                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                            {{-- Addresses --}}
                            <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="delivery-heading">
                                <h2 id="delivery-heading" class="font-display text-lg font-bold text-navy-900">Delivery details</h2>
                                <dl class="mt-5 space-y-4 text-sm">
                                    <div>
                                        <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Ship to</dt>
                                        <dd class="mt-1 leading-relaxed text-navy-700">{!! $order['shipping_address']['html'] !!}</dd>
                                    </div>
                                    @if ($order['delivery_instructions'])
                                        <div>
                                            <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Instructions</dt>
                                            <dd class="mt-1 text-navy-700">{{ $order['delivery_instructions'] }}</dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Billing</dt>
                                        <dd class="mt-1 leading-relaxed text-navy-700">{!! $order['billing_address']['html'] !!}</dd>
                                    </div>
                                </dl>
                            </section>

                            {{-- Order summary --}}
                            <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="summary-heading">
                                <h2 id="summary-heading" class="font-display text-lg font-bold text-navy-900">Order summary</h2>
                                <dl class="mt-5 space-y-2 text-sm text-navy-700">
                                    <div class="flex justify-between"><dt>Subtotal</dt><dd class="tabular-nums">{{ $order['summary']['subtotal'] }}</dd></div>
                                    <div class="flex justify-between"><dt>Discount</dt><dd class="tabular-nums">{{ $order['summary']['discount'] }}</dd></div>
                                    <div class="flex justify-between"><dt>Shipping</dt><dd class="tabular-nums">{{ $order['summary']['shipping'] }}</dd></div>
                                    <div class="flex justify-between"><dt>Tax</dt><dd class="tabular-nums">{{ $order['summary']['tax'] }}</dd></div>
                                    <div class="flex justify-between border-t border-navy-100 pt-3 font-semibold text-navy-900"><dt>Total</dt><dd class="tabular-nums">{{ $order['summary']['total'] }}</dd></div>
                                </dl>
                                <div class="mt-6 flex flex-col gap-2">
                                    @if ($order['is_delivered'])
                                        <x-ui.button :href="route('account.reviews').'#ready-for-review'" variant="secondary" size="sm">
                                            Write a review
                                        </x-ui.button>
                                    @endif
                                    <x-ui.button :href="route('account.orders')" variant="outline" size="sm">Back to orders</x-ui.button>
                                    <x-ui.button :href="route('support')" variant="ghost" size="sm">Need help with this order?</x-ui.button>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
