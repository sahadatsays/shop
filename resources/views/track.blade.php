@php
    $timeline = $tracking['timeline'];
    $orderItems = $tracking['items'];
    $backUrl = $backUrl ?? route('account.orders');
@endphp

<x-layouts.app title="Track Shipment"
    description="Follow your Jackpot BD LTD order in real time — live status, courier details, and estimated arrival.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-track>

        {{-- Header --}}
        <nav aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                <li><a href="{{ route('account') }}"
                        class="transition-colors duration-200 hover:text-navy-900">Account</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('account.orders') }}"
                        class="transition-colors duration-200 hover:text-navy-900">Orders</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="font-medium text-navy-900">Track {{ $tracking['order_number_display'] }}
                </li>
            </ol>
        </nav>

        <div class="mt-4 flex flex-wrap items-baseline gap-3">
            <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">Track your shipment</h1>
            <p class="text-navy-500">Order {{ $tracking['order_number_display'] }}</p>
        </div>

        {{-- Estimated arrival banner --}}
        <section class="relative mt-8 overflow-hidden rounded-card bg-navy-900 p-8 text-white sm:p-10"
            aria-labelledby="eta-heading">
            <div class="absolute -top-24 -right-16 size-64 rounded-full bg-olive-600/30 blur-3xl" aria-hidden="true">
            </div>
            <div class="absolute -bottom-24 right-40 size-52 rounded-full bg-bronze-500/20 blur-3xl" aria-hidden="true">
            </div>
            <div class="relative flex flex-wrap items-end justify-between gap-6">
                <div>
                    <p id="eta-heading" class="flex items-center gap-2 text-sm font-medium text-navy-200">
                        <span class="relative flex size-2.5" aria-hidden="true">
                            <span
                                class="absolute inline-flex size-full animate-ping rounded-full bg-bronze-400 opacity-70"></span>
                            <span class="relative inline-flex size-2.5 rounded-full bg-bronze-400"></span>
                        </span>
                        {{ $tracking['eta_heading'] }}
                    </p>
                    <p class="mt-2 font-display text-3xl font-extrabold sm:text-4xl">{{ $tracking['eta_detail'] }}</p>
                    <p class="mt-2 text-navy-200">Placed {{ $tracking['placed_at'] }} ·
                        {{ $tracking['payment_status'] }} via {{ $tracking['payment_method'] }}</p>
                </div>
                <div class="w-full max-w-xs">
                    <div class="flex items-center justify-between text-xs font-medium text-navy-200">
                        <span>{{ $tracking['progress_step'] }} of {{ $tracking['progress_total'] }} steps</span>
                        <span>{{ $tracking['progress_percent'] }}%</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/20">
                        <div class="h-full rounded-full bg-bronze-400"
                            style="width: {{ $tracking['progress_percent'] }}%" aria-hidden="true"></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Horizontal step strip (compact overview) --}}
        <nav aria-label="Shipment milestones"
            class="mt-6 overflow-x-auto rounded-card bg-surface p-5 shadow-soft scrollbar-none">
            <ol class="flex min-w-max items-center gap-0 px-2 sm:min-w-0 sm:justify-between">
                @foreach ($timeline as $step)
                    @if (!$loop->first)
                        <li aria-hidden="true"
                            class="h-px w-6 shrink-0 -translate-y-3 sm:w-auto sm:flex-1 {{ $timeline[$loop->index - 1]['state'] === 'done' ? 'bg-olive-600' : 'bg-navy-200' }}">
                        </li>
                    @endif
                    <li class="flex flex-col items-center gap-2 px-1">
                        <span
                            class="flex size-7 items-center justify-center rounded-full sm:size-8
                            {{ $step['state'] === 'done' ? 'bg-olive-600 text-white' : '' }}
                            {{ $step['state'] === 'current' ? 'bg-navy-900 text-white ring-4 ring-navy-900/10' : '' }}
                            {{ $step['state'] === 'upcoming' ? 'border border-navy-200 bg-surface' : '' }}">
                            @if ($step['state'] === 'done')
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                    aria-hidden="true">
                                    <path d="m5 13 4 4L19 7" />
                                </svg>
                            @elseif ($step['state'] === 'current')
                                <span class="size-1.5 animate-ping rounded-full bg-bronze-400"
                                    aria-hidden="true"></span>
                            @else
                                <span class="size-1.5 rounded-full bg-navy-300" aria-hidden="true"></span>
                            @endif
                        </span>
                        <span
                            class="max-w-16 text-center text-[0.65rem] font-semibold leading-tight sm:max-w-none sm:text-xs {{ $step['state'] === 'upcoming' ? 'text-navy-400' : 'text-navy-900' }}">{{ $step['label'] }}</span>
                    </li>
                @endforeach
            </ol>
        </nav>

        <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-5">

            {{-- ============ Timeline ============ --}}
            <section class="rounded-card bg-surface p-7 shadow-soft lg:col-span-2" aria-labelledby="timeline-heading">
                <h2 id="timeline-heading" class="font-display text-lg font-bold text-navy-900">Shipment progress</h2>
                <ol class="mt-6">
                    @foreach ($timeline as $step)
                        <li class="relative flex gap-4 {{ $loop->last ? '' : 'pb-8' }}">
                            @unless ($loop->last)
                                <span
                                    class="absolute top-8 left-3.75 h-full w-0.5 {{ $step['state'] === 'done' ? 'bg-olive-600' : 'bg-navy-200' }}"
                                    aria-hidden="true"></span>
                            @endunless
                            <span
                                class="relative flex size-8 shrink-0 items-center justify-center rounded-full
                                {{ $step['state'] === 'done' ? 'bg-olive-600 text-white' : '' }}
                                {{ $step['state'] === 'current' ? 'bg-navy-900 text-white ring-4 ring-navy-900/10' : '' }}
                                {{ $step['state'] === 'upcoming' ? 'border border-navy-200 bg-surface text-navy-400' : '' }}">
                                @if ($step['state'] === 'done')
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <path d="m5 13 4 4L19 7" />
                                    </svg>
                                @elseif ($step['state'] === 'current')
                                    <span class="size-2 animate-ping rounded-full bg-bronze-400"
                                        aria-hidden="true"></span>
                                @else
                                    <span class="size-2 rounded-full bg-navy-300" aria-hidden="true"></span>
                                @endif
                            </span>
                            <div class="pt-1">
                                <p
                                    class="text-sm font-semibold {{ $step['state'] === 'upcoming' ? 'text-navy-400' : 'text-navy-900' }}">
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

                {{-- Items in shipment --}}
                <div class="mt-4 border-t border-navy-100 pt-6">
                    <h3 class="text-sm font-semibold text-navy-900">In this shipment</h3>
                    <ul class="mt-3 flex items-center gap-3">
                        @foreach ($orderItems as $item)
                            <li>
                                <a href="{{ $item['url'] }}" title="{{ $item['name'] }}"
                                    class="block size-14 overflow-hidden rounded-xl bg-navy-100 ring-1 ring-navy-900/5 transition-transform duration-200 hover:scale-105">
                                    @if ($item['image'])
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy"
                                            class="size-full object-cover">
                                    @endif
                                </a>
                            </li>
                        @endforeach
                        <li class="text-sm text-navy-500">{{ $tracking['item_count'] }}
                            {{ $tracking['item_count'] === 1 ? 'item' : 'items' }}</li>
                    </ul>
                </div>

                <div class="mt-6 border-t border-navy-100 pt-6">
                    <h3 class="text-sm font-semibold text-navy-900">Order summary</h3>
                    <dl class="mt-3 space-y-2 text-sm text-navy-700">
                        <div class="flex justify-between">
                            <dt>Subtotal</dt>
                            <dd class="tabular-nums">{{ $tracking['summary']['subtotal'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Discount</dt>
                            <dd class="tabular-nums">{{ $tracking['summary']['discount'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Shipping</dt>
                            <dd class="tabular-nums">{{ $tracking['summary']['shipping'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Tax</dt>
                            <dd class="tabular-nums">{{ $tracking['summary']['tax'] }}</dd>
                        </div>
                        <div class="flex justify-between border-t border-navy-100 pt-2 font-semibold text-navy-900">
                            <dt>Total</dt>
                            <dd class="tabular-nums">{{ $tracking['summary']['total'] }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            {{-- ============ Map + courier ============ --}}
            <div class="space-y-8 lg:col-span-3">

                {{-- Map --}}
                <section class="overflow-hidden rounded-card bg-surface shadow-soft" aria-label="Delivery route map">
                    <div class="relative">
                        <svg viewBox="0 0 800 420" class="block w-full" role="img"
                            aria-label="Stylized map showing the courier en route between the distribution center and your address">
                            <rect width="800" height="420" class="fill-navy-50" />
                            <rect x="60" y="52" width="150" height="104" rx="12"
                                class="fill-olive-100" />
                            <rect x="580" y="260" width="150" height="110" rx="12"
                                class="fill-olive-100" />
                            <circle cx="135" cy="104" r="17" class="fill-olive-200" />
                            <circle cx="655" cy="315" r="20" class="fill-olive-200" />
                            <path d="M0 330c90-40 140 30 230 0s130 40 210 16v74H0v-90Z" class="fill-navy-100" />
                            <g class="stroke-white" stroke-width="14" stroke-linecap="round">
                                <path d="M40 200h720" />
                                <path d="M240 30v360" />
                                <path d="M470 30v360" />
                                <path d="M40 90h720" />
                                <path d="M40 300h430" />
                                <path d="M620 90v270" />
                            </g>
                            <g class="stroke-navy-200/70" stroke-width="2" stroke-dasharray="8 10">
                                <path d="M40 200h720" />
                                <path d="M240 30v360" />
                                <path d="M470 30v360" />
                            </g>
                            <path id="route" d="M110 90 H240 V200 H470 V300 H620 V332" fill="none"
                                class="stroke-navy-900" stroke-width="5" stroke-linecap="round"
                                stroke-dasharray="14 10" />
                            <g>
                                <circle cx="110" cy="90" r="13" class="fill-olive-600" />
                                <circle cx="110" cy="90" r="5" class="fill-white" />
                            </g>
                            <g>
                                <path d="M620 302c-12 0-21 9-21 21 0 15 21 33 21 33s21-18 21-33c0-12-9-21-21-21Z"
                                    class="fill-bronze-500" />
                                <circle cx="620" cy="323" r="8" class="fill-white" />
                            </g>
                            <g>
                                <circle r="15" class="fill-navy-900">
                                    <animateMotion dur="14s" repeatCount="indefinite" rotate="0"
                                        keyPoints="0;0.72;0.72;1;1" keyTimes="0;0.55;0.7;0.95;1" calcMode="linear">
                                        <mpath href="#route" />
                                    </animateMotion>
                                </circle>
                                <g class="fill-bronze-400">
                                    <path d="M-7-3h8v6h-8zM1-1h4l2 2v2H1z" transform="scale(1.1)">
                                        <animateMotion dur="14s" repeatCount="indefinite"
                                            keyPoints="0;0.72;0.72;1;1" keyTimes="0;0.55;0.7;0.95;1"
                                            calcMode="linear">
                                            <mpath href="#route" />
                                        </animateMotion>
                                    </path>
                                </g>
                            </g>
                        </svg>

                        <div
                            class="absolute bottom-4 left-4 flex flex-wrap gap-x-5 gap-y-1.5 rounded-xl bg-white/90 px-4 py-2.5 text-xs font-medium text-navy-700 shadow-soft backdrop-blur-sm">
                            <span class="flex items-center gap-2"><span class="size-2.5 rounded-full bg-olive-600"
                                    aria-hidden="true"></span>Distribution center</span>
                            <span class="flex items-center gap-2"><span class="size-2.5 rounded-full bg-navy-900"
                                    aria-hidden="true"></span>Courier</span>
                            <span class="flex items-center gap-2"><span class="size-2.5 rounded-full bg-bronze-500"
                                    aria-hidden="true"></span>Your address</span>
                        </div>
                    </div>
                </section>

                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                    {{-- Courier information --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="courier-heading">
                        <h2 id="courier-heading" class="font-display text-lg font-bold text-navy-900">Courier</h2>
                        <div class="mt-5 flex items-center gap-4">
                            <span
                                class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-navy-900 text-bronze-400">
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                                    aria-hidden="true">
                                    <path
                                        d="M3 7h11v8H3zM14 10h4l3 3v2h-7zM7 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm11 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-semibold text-navy-900">
                                    {{ $tracking['courier_name'] ?? 'Courier pending' }}</p>
                                <p class="text-sm text-navy-500">{{ $tracking['status'] }}</p>
                            </div>
                        </div>

                        <dl class="mt-5 space-y-3 border-t border-navy-100 pt-5 text-sm">
                            <div>
                                <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Tracking number
                                </dt>
                                <dd class="mt-1 flex items-center gap-2">
                                    @if ($tracking['tracking_number_display'])
                                        <code
                                            class="rounded-lg bg-navy-50 px-2.5 py-1 font-mono text-xs font-semibold text-navy-800"
                                            data-tracking-number>{{ $tracking['tracking_number_display'] }}</code>
                                        <button type="button" data-copy-tracking aria-label="Copy tracking number"
                                            class="flex size-8 items-center justify-center rounded-lg text-navy-500 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-900">
                                            <svg data-copy-icon class="size-4" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <rect x="9" y="9" width="12" height="12" rx="2" />
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                            </svg>
                                            <svg data-copied-icon hidden class="size-4 text-green-600"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                                                aria-hidden="true">
                                                <path d="m5 13 4 4L19 7" />
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-navy-500">Available once your order ships.</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Service</dt>
                                <dd class="mt-1 text-navy-700">{{ $tracking['payment_method'] }} ·
                                    {{ $tracking['payment_status'] }}</dd>
                            </div>
                        </dl>

                        <x-ui.button variant="outline" size="sm" class="mt-6 w-full">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <path d="M21 12a9 9 0 1 0-3.5 7.1L21 21l-1-3.4A8.96 8.96 0 0 0 21 12ZM8 10h8M8 14h5" />
                            </svg>
                            Contact courier support
                        </x-ui.button>
                    </section>

                    {{-- Delivery details --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="delivery-heading">
                        <h2 id="delivery-heading" class="font-display text-lg font-bold text-navy-900">Delivery
                            details</h2>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div>
                                <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Deliver to</dt>
                                <dd class="mt-1 leading-relaxed text-navy-700">{!! $tracking['shipping_address']['html'] !!}</dd>
                            </div>
                            @if ($tracking['delivery_instructions'])
                                <div>
                                    <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">
                                        Instructions</dt>
                                    <dd class="mt-1 text-navy-700">{{ $tracking['delivery_instructions'] }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Billing</dt>
                                <dd class="mt-1 leading-relaxed text-navy-700">{!! $tracking['billing_address']['html'] !!}</dd>
                            </div>
                        </dl>
                        <div class="mt-6 flex flex-col gap-2">
                            <x-ui.button variant="ghost" size="sm">Update delivery instructions</x-ui.button>
                            <x-ui.button :href="$backUrl" variant="outline" size="sm">Back to
                                orders</x-ui.button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
