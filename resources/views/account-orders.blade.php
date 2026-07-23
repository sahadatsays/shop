@php
    $steps = ['Order placed', 'Packed', 'Shipped', 'Delivered'];
@endphp

<x-layouts.app :title="$title" description="Every Valor Supply Co. order in one place — track shipments, download invoices, and reorder your favorites.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-orders>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">

            <x-account.sidebar active="Orders" />

            {{-- ============ Main ============ --}}
            <div class="lg:col-span-3">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                        <li><a href="{{ route('account') }}" class="transition-colors duration-200 hover:text-navy-900">Account</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="font-medium text-navy-900">Orders</li>
                    </ol>
                </nav>

                <div class="mt-4 flex flex-wrap items-baseline gap-3">
                    <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">Order history</h1>
                    <p class="text-navy-500" data-orders-count>{{ $ordersCount }} {{ $ordersCount === 1 ? 'order' : 'orders' }}</p>
                </div>

                {{-- Status filters --}}
                <div class="mt-6 flex flex-wrap gap-2" role="group" aria-label="Filter orders by status">
                    @foreach ($filters as $filter)
                        <button type="button" data-orders-filter="{{ $filter }}" aria-pressed="{{ $filter === $activeFilter ? 'true' : 'false' }}"
                                class="rounded-full border px-4 py-2 text-sm font-medium transition-colors duration-200
                                    aria-pressed:border-navy-900 aria-pressed:bg-navy-900 aria-pressed:text-white
                                    border-navy-200 bg-surface text-navy-700 hover:border-navy-300 hover:bg-navy-50">
                            {{ $filter }}
                        </button>
                    @endforeach
                </div>

                {{-- Order cards --}}
                <div class="mt-8 space-y-6">
                    @foreach ($orders as $order)
                        <article data-order-card data-status="{{ $order['status'] }}"
                                 class="overflow-hidden rounded-card bg-surface shadow-soft transition-shadow duration-300 hover:shadow-card">

                            {{-- Card header --}}
                            <header class="flex flex-wrap items-center gap-x-6 gap-y-3 border-b border-navy-100 bg-navy-50/60 px-7 py-5">
                                <div>
                                    <p class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Order</p>
                                    <p class="font-display text-base font-bold text-navy-900">{{ $order['number'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Placed</p>
                                    <p class="text-sm font-medium text-navy-800">{{ $order['placed'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold tracking-wide text-navy-500 uppercase">Total</p>
                                    <p class="text-sm font-bold text-navy-900 tabular-nums">{{ $order['total'] }}</p>
                                </div>
                                <div class="ml-auto">
                                    <x-ui.badge :variant="$order['status_variant']">{{ $order['status'] }}</x-ui.badge>
                                </div>
                            </header>

                            <div class="px-7 py-6">
                                {{-- Status timeline --}}
                                <ol class="flex items-center" aria-label="Order progress: {{ $order['status'] }}">
                                    @foreach ($steps as $stepIndex => $step)
                                        @php
                                            $stepNumber = $stepIndex + 1;
                                            $isDone = $stepNumber < $order['progress'];
                                            $isCurrent = $stepNumber === $order['progress'];
                                            $isComplete = $order['progress'] >= count($steps);
                                        @endphp
                                        @if (!$loop->first)
                                            <li aria-hidden="true" class="h-0.5 flex-1 -translate-y-2.5 rounded-full sm:-translate-y-3 {{ $stepNumber <= $order['progress'] ? 'bg-olive-600' : 'bg-navy-200' }}"></li>
                                        @endif
                                        <li class="flex flex-col items-center gap-1.5 px-1 sm:px-2">
                                            <span class="flex size-6 items-center justify-center rounded-full sm:size-7
                                                {{ $isDone || ($isCurrent && $isComplete) ? 'bg-olive-600 text-white' : '' }}
                                                {{ $isCurrent && !$isComplete ? 'bg-navy-900 text-white ring-4 ring-navy-900/10' : '' }}
                                                {{ !$isDone && !$isCurrent ? 'border border-navy-200 bg-surface' : '' }}">
                                                @if ($isDone || ($isCurrent && $isComplete))
                                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                                                @elseif ($isCurrent)
                                                    <span class="size-1.5 animate-ping rounded-full bg-bronze-400" aria-hidden="true"></span>
                                                @else
                                                    <span class="size-1.5 rounded-full bg-navy-300" aria-hidden="true"></span>
                                                @endif
                                            </span>
                                            <span class="text-[0.65rem] font-semibold whitespace-nowrap sm:text-xs {{ $stepNumber <= $order['progress'] ? 'text-navy-900' : 'text-navy-400' }}">{{ $step }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                                <p class="mt-3 text-center text-xs font-medium text-navy-500">{{ $order['eta'] }}</p>

                                {{-- Items + actions --}}
                                <div class="mt-6 flex flex-wrap items-center gap-6 border-t border-navy-100 pt-6">
                                    <ul class="flex items-center gap-3" aria-label="Items in this order">
                                        @foreach ($order['items'] as $item)
                                            <li>
                                                <a href="{{ $item['url'] }}" title="{{ $item['name'] }}"
                                                   class="block size-14 overflow-hidden rounded-xl bg-navy-100 ring-1 ring-navy-900/5 transition-transform duration-200 hover:scale-105">
                                                    @if ($item['image'])
                                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy" class="size-full object-cover">
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                        <li class="text-sm text-navy-500">{{ count($order['items']) }} {{ count($order['items']) === 1 ? 'item' : 'items' }}</li>
                                    </ul>

                                    <div class="ml-auto flex flex-wrap gap-2">
                                        <x-ui.button variant="ghost" size="sm">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>
                                            </svg>
                                            Invoice
                                        </x-ui.button>
                                        <x-ui.button variant="outline" size="sm">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M17 2v4H7a4 4 0 0 0 0 8h1m-1 8v-4h10a4 4 0 0 0 0-8h-1"/>
                                            </svg>
                                            Reorder
                                        </x-ui.button>
                                        @if (! $order['is_delivered'])
                                            <x-ui.button :href="$order['track_url']" size="sm">
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M12 21s-6-5.5-6-10a6 6 0 0 1 12 0c0 4.5-6 10-6 10Zm0-7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                                                </svg>
                                                Track order
                                            </x-ui.button>
                                        @else
                                            <x-ui.button variant="secondary" size="sm">
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M12 2.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.3l-5.8 3.1 1.1-6.5L2.6 9.3l6.5-.9L12 2.5Z"/>
                                                </svg>
                                                Write a review
                                            </x-ui.button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- No results for filter --}}
                <div data-orders-empty hidden class="mx-auto max-w-md py-16 text-center">
                    <svg class="mx-auto h-32 w-auto" viewBox="0 0 200 140" fill="none" aria-hidden="true">
                        <circle cx="100" cy="66" r="50" class="fill-navy-100"/>
                        <rect x="66" y="44" width="68" height="48" rx="8" class="fill-white stroke-navy-900" stroke-width="3.5"/>
                        <path d="M66 58h68M80 72h24" class="stroke-navy-300" stroke-width="3" stroke-linecap="round"/>
                        <path d="M152 30c3 0 5.5-2.5 5.5-5.5 0 3 2.5 5.5 5.5 5.5-3 0-5.5 2.5-5.5 5.5 0-3-2.5-5.5-5.5-5.5Z" class="fill-bronze-400"/>
                    </svg>
                    <h2 class="mt-5 font-display text-xl font-bold text-navy-900">No orders with this status</h2>
                    <p class="mt-2 text-navy-600">Try another filter, or check back once your next order ships.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
