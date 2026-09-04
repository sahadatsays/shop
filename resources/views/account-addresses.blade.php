@php
    $addresses = [
        [
            'id' => 'home',
            'label' => 'Home',
            'icon' => 'home',
            'default' => true,
            'name' => 'James Mitchell',
            'line1' => '1247 Cedar Ridge Drive',
            'line2' => 'Unit B',
            'city' => 'Springfield',
            'state' => 'IL',
            'postal' => '62704',
            'country' => 'United States',
            'phone' => '+1 (217) 555-0142',
        ],
        [
            'id' => 'office',
            'label' => 'Office',
            'icon' => 'office',
            'default' => false,
            'name' => 'James Mitchell',
            'line1' => '880 Veterans Parkway',
            'line2' => 'Suite 210',
            'city' => 'Chicago',
            'state' => 'IL',
            'postal' => '60607',
            'country' => 'United States',
            'phone' => '+1 (312) 555-0198',
        ],
    ];
@endphp

<x-layouts.app title="Saved Addresses"
    description="Manage shipping addresses for your Jackpot BD LTD account.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-addresses>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">

            <x-account.sidebar active="Addresses" />

            <div class="lg:col-span-3">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                        <li><a href="{{ route('account') }}"
                                class="transition-colors duration-200 hover:text-navy-900">Account</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="font-medium text-navy-900">Addresses</li>
                    </ol>
                </nav>

                <div class="mt-4 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">Saved addresses</h1>
                        <p class="mt-2 text-navy-600">Where we ship your gear — add, edit, or set your default delivery
                            location.</p>
                    </div>
                    <x-ui.button variant="primary" size="sm" data-address-add class="shrink-0">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        Add address
                    </x-ui.button>
                </div>

                <p class="mt-6 text-sm text-navy-500" data-address-count aria-live="polite">{{ count($addresses) }}
                    saved {{ Str::plural('address', count($addresses)) }}</p>

                <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2" data-address-grid>

                    @foreach ($addresses as $address)
                        <article data-address-card data-address-id="{{ $address['id'] }}"
                            data-default="{{ $address['default'] ? 'true' : 'false' }}"
                            data-label="{{ $address['label'] }}" data-name="{{ $address['name'] }}"
                            data-line1="{{ $address['line1'] }}" data-line2="{{ $address['line2'] }}"
                            data-city="{{ $address['city'] }}" data-state="{{ $address['state'] }}"
                            data-postal="{{ $address['postal'] }}" data-country="{{ $address['country'] }}"
                            data-phone="{{ $address['phone'] }}"
                            class="group flex flex-col overflow-hidden rounded-card bg-surface shadow-soft transition-all duration-300 ease-out hover:-translate-y-0.5 hover:shadow-card {{ $address['default'] ? 'ring-2 ring-bronze-400/60 ring-offset-2 ring-offset-canvas' : '' }}"
                            aria-labelledby="address-{{ $address['id'] }}-label">
                            {{-- Map placeholder --}}
                            <div class="relative h-36 overflow-hidden bg-navy-50" aria-hidden="true">
                                <svg viewBox="0 0 400 144" class="block h-full w-full"
                                    preserveAspectRatio="xMidYMid slice">
                                    <rect width="400" height="144" class="fill-navy-50" />
                                    <rect x="24" y="20" width="80" height="56" rx="8"
                                        class="fill-olive-100" />
                                    <rect x="280" y="72" width="96" height="52" rx="8"
                                        class="fill-olive-100" />
                                    <path d="M0 112c48-18 72 12 120 0s72 18 120 6v26H0v-32Z" class="fill-navy-100" />
                                    <g class="stroke-white" stroke-width="8" stroke-linecap="round">
                                        <path d="M16 72h368" />
                                        <path d="M120 12v120" />
                                        <path d="M260 12v120" />
                                        <path d="M16 36h368" />
                                    </g>
                                    <g class="stroke-navy-200/60" stroke-width="1.5" stroke-dasharray="6 8">
                                        <path d="M16 72h368" />
                                        <path d="M120 12v120" />
                                    </g>
                                    <path d="M200 28c-14 0-25 11-25 25 0 18 25 40 25 40s25-22 25-40c0-14-11-25-25-25Z"
                                        class="fill-bronze-500" />
                                    <circle cx="200" cy="53" r="7" class="fill-white" />
                                </svg>
                                <div
                                    class="absolute inset-x-0 bottom-0 flex items-center justify-between bg-linear-to-t from-navy-900/70 to-transparent px-4 py-3">
                                    <span class="text-xs font-medium text-white/90">Map preview</span>
                                    <span
                                        class="rounded-md bg-white/15 px-2 py-0.5 text-[0.65rem] font-semibold tracking-wide text-white uppercase backdrop-blur-sm">Maps
                                        API</span>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-6">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-navy-900/5 text-navy-700">
                                            @if ($address['icon'] === 'home')
                                                <svg class="size-5" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 11 12 3l9 8M6 10v10h12V10" />
                                                </svg>
                                            @else
                                                <svg class="size-5" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z" />
                                                    <path d="M6 12h12M10 6h4M10 10h4M10 14h4M10 18h4" />
                                                </svg>
                                            @endif
                                        </span>
                                        <div>
                                            <h2 id="address-{{ $address['id'] }}-label"
                                                class="font-display text-lg font-bold text-navy-900"
                                                data-address-label-el>{{ $address['label'] }}</h2>
                                            <p class="text-sm text-navy-500" data-address-name-el>
                                                {{ $address['name'] }}</p>
                                        </div>
                                    </div>
                                    @if ($address['default'])
                                        <x-ui.badge variant="bronze" data-default-badge>Default</x-ui.badge>
                                    @endif
                                </div>

                                <address class="mt-4 not-italic text-sm leading-relaxed text-navy-700"
                                    data-address-text>
                                    {{ $address['line1'] }}<br>
                                    @if ($address['line2'])
                                        {{ $address['line2'] }}<br>
                                    @endif
                                    {{ $address['city'] }}, {{ $address['state'] }} {{ $address['postal'] }}<br>
                                    {{ $address['country'] }}
                                </address>

                                <p class="mt-3 text-sm text-navy-500" data-address-phone-el>{{ $address['phone'] }}</p>

                                <div class="mt-auto flex flex-wrap items-center gap-2 pt-6">
                                    @unless ($address['default'])
                                        <button type="button" data-address-set-default
                                            class="rounded-xl px-3 py-2 text-xs font-semibold text-olive-700 transition-colors duration-200 hover:bg-olive-50">
                                            Set as default
                                        </button>
                                    @endunless
                                    <div class="ml-auto flex gap-2">
                                        <button type="button" data-address-edit
                                            aria-label="Edit {{ $address['label'] }} address"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-navy-200 bg-surface px-3 py-2 text-xs font-semibold text-navy-900 transition-colors duration-200 hover:border-navy-300 hover:bg-navy-50">
                                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <button type="button" data-address-delete
                                            aria-label="Delete {{ $address['label'] }} address"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-surface px-3 py-2 text-xs font-semibold text-red-600 transition-colors duration-200 hover:border-red-300 hover:bg-red-50">
                                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <path
                                                    d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach

                    {{-- Add address card --}}
                    <button type="button" data-address-add-card
                        class="flex min-h-72 flex-col items-center justify-center gap-4 rounded-card border-2 border-dashed border-navy-200 bg-surface/50 p-8 text-center shadow-soft transition-all duration-300 ease-out hover:-translate-y-0.5 hover:border-bronze-400 hover:bg-surface hover:shadow-card focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bronze-500 sm:min-h-0">
                        <span
                            class="flex size-14 items-center justify-center rounded-full bg-navy-900/5 text-navy-700 transition-colors duration-200 group-hover:bg-bronze-500/10">
                            <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                        </span>
                        <span>
                            <span class="block font-display text-lg font-bold text-navy-900">Add address</span>
                            <span class="mt-1 block text-sm text-navy-500">Save a new shipping
                                location</span>
                        </span>
                    </button>
                </div>

                {{-- Empty state (hidden until all addresses removed) --}}
                <div data-addresses-empty hidden
                    class="mt-8 rounded-card border border-navy-100 bg-surface p-12 text-center shadow-soft">
                    <span
                        class="mx-auto flex size-16 items-center justify-center rounded-full bg-navy-900/5 text-navy-500">
                        <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path
                                d="M12 21s-6-5.5-6-10a6 6 0 0 1 12 0c0 4.5-6 10-6 10Zm0-7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                        </svg>
                    </span>
                    <h2 class="mt-5 font-display text-xl font-bold text-navy-900">No saved addresses yet</h2>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-navy-600">Add your first address to speed up checkout
                        and keep deliveries on track.</p>
                    <x-ui.button variant="primary" size="sm" data-address-add class="mt-6">Add your first
                        address</x-ui.button>
                </div>

                <p class="mt-8 text-sm text-navy-500" data-address-status aria-live="polite"></p>
            </div>
        </div>

        {{-- Add / Edit dialog --}}
        <dialog data-address-dialog
            class="w-full max-w-lg rounded-card border-0 bg-surface p-0 shadow-glass backdrop:bg-navy-900/40 open:animate-scale-in">
            <form method="dialog" class="p-7" data-address-form novalidate>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-display text-xl font-bold text-navy-900" data-address-dialog-title>Add address
                        </h2>
                        <p class="mt-1 text-sm text-navy-500">We’ll use this for shipping unless you choose otherwise
                            at checkout.</p>
                    </div>
                    <button type="button" data-address-dialog-close aria-label="Close dialog"
                        class="flex size-9 shrink-0 items-center justify-center rounded-xl text-navy-500 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-900">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="address-label" class="block text-sm font-medium text-navy-900">Address
                                label</label>
                            <select id="address-label" name="label" required
                                class="mt-1.5 block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                <option value="Home">Home</option>
                                <option value="Office">Office</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <x-ui.input name="full-name" label="Full name" autocomplete="name"
                                value="James Mitchell" required />
                        </div>
                        <div class="sm:col-span-2">
                            <x-ui.input name="line1" label="Street address" autocomplete="address-line1"
                                required />
                        </div>
                        <div class="sm:col-span-2">
                            <x-ui.input name="line2" label="Apt, suite, unit (optional)"
                                autocomplete="address-line2" />
                        </div>
                        <x-ui.input name="city" label="City" autocomplete="address-level2" required />
                        <x-ui.input name="state" label="State / Province" autocomplete="address-level1" required />
                        <x-ui.input name="postal" label="ZIP / Postal code" autocomplete="postal-code" required />
                        <div>
                            <label for="address-country"
                                class="block text-sm font-medium text-navy-900">Country</label>
                            <select id="address-country" name="country" required
                                class="mt-1.5 block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                <option value="United States" selected>United States</option>
                                <option value="Canada">Canada</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Australia">Australia</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <x-ui.input name="phone" type="tel" label="Phone (optional)" autocomplete="tel" />
                        </div>
                    </div>

                    {{-- Map picker placeholder --}}
                    <div class="overflow-hidden rounded-xl border border-navy-100">
                        <div class="relative h-40 bg-navy-50">
                            <svg viewBox="0 0 480 160" class="block h-full w-full"
                                preserveAspectRatio="xMidYMid slice" role="img"
                                aria-label="Map location picker placeholder">
                                <rect width="480" height="160" class="fill-navy-50" />
                                <rect x="32" y="24" width="100" height="64" rx="10"
                                    class="fill-olive-100" />
                                <rect x="340" y="80" width="108" height="56" rx="10"
                                    class="fill-olive-100" />
                                <g class="stroke-white" stroke-width="10" stroke-linecap="round">
                                    <path d="M20 80h440" />
                                    <path d="M160 16v128" />
                                    <path d="M320 16v128" />
                                </g>
                                <path d="M240 48c-16 0-28 12-28 28 0 20 28 44 28 44s28-24 28-44c0-16-12-28-28-28Z"
                                    class="fill-bronze-500" />
                                <circle cx="240" cy="76" r="8" class="fill-white" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-navy-900/5">
                                <svg class="size-8 text-navy-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path
                                        d="M12 21s-6-5.5-6-10a6 6 0 0 1 12 0c0 4.5-6 10-6 10Zm0-7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                                </svg>
                                <p class="mt-2 text-xs font-medium text-navy-600">Pin location on map</p>
                                <p class="text-[0.65rem] text-navy-400">Google Maps integration coming soon</p>
                            </div>
                        </div>
                    </div>

                    <label class="flex cursor-pointer items-center gap-3">
                        <input type="checkbox" name="set-default" data-address-set-default-input
                            class="size-4.5 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                        <span class="text-sm text-navy-700">Set as default shipping address</span>
                    </label>
                </div>

                <div class="mt-8 flex flex-wrap justify-end gap-3">
                    <x-ui.button variant="outline" size="sm" type="button"
                        data-address-dialog-close>Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" data-address-save>
                        <span data-address-save-label>Save address</span>
                    </x-ui.button>
                </div>
            </form>
        </dialog>

        {{-- Delete confirmation --}}
        <dialog data-address-delete-dialog
            class="w-full max-w-md rounded-card border-0 bg-surface p-0 shadow-glass backdrop:bg-navy-900/40 open:animate-scale-in">
            <div class="p-7">
                <div class="flex size-12 items-center justify-center rounded-full bg-red-50 text-red-600">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path
                            d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                    </svg>
                </div>
                <h2 class="mt-5 font-display text-xl font-bold text-navy-900">Delete this address?</h2>
                <p class="mt-2 text-sm text-navy-600">This removes <strong data-delete-label>Home</strong> from your
                    saved addresses. You can add it again anytime.</p>
                <div class="mt-8 flex flex-wrap justify-end gap-3">
                    <x-ui.button variant="outline" size="sm" type="button" data-delete-cancel>Keep
                        address</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="button" data-delete-confirm
                        class="!bg-red-600 hover:!bg-red-700">Delete address</x-ui.button>
                </div>
            </div>
        </dialog>

        {{-- Card template for JS-created addresses --}}
        <template data-address-template>
            <article data-address-card data-default="false"
                class="group flex flex-col overflow-hidden rounded-card bg-surface shadow-soft transition-all duration-300 ease-out hover:-translate-y-0.5 hover:shadow-card">
                <div class="relative h-36 overflow-hidden bg-navy-50" aria-hidden="true">
                    <svg viewBox="0 0 400 144" class="block h-full w-full" preserveAspectRatio="xMidYMid slice">
                        <rect width="400" height="144" class="fill-navy-50" />
                        <rect x="24" y="20" width="80" height="56" rx="8" class="fill-olive-100" />
                        <rect x="280" y="72" width="96" height="52" rx="8" class="fill-olive-100" />
                        <path d="M0 112c48-18 72 12 120 0s72 18 120 6v26H0v-32Z" class="fill-navy-100" />
                        <g class="stroke-white" stroke-width="8" stroke-linecap="round">
                            <path d="M16 72h368" />
                            <path d="M120 12v120" />
                            <path d="M260 12v120" />
                            <path d="M16 36h368" />
                        </g>
                        <path d="M200 28c-14 0-25 11-25 25 0 18 25 40 25 40s25-22 25-40c0-14-11-25-25-25Z"
                            class="fill-bronze-500" />
                        <circle cx="200" cy="53" r="7" class="fill-white" />
                    </svg>
                    <div
                        class="absolute inset-x-0 bottom-0 flex items-center justify-between bg-linear-to-t from-navy-900/70 to-transparent px-4 py-3">
                        <span class="text-xs font-medium text-white/90">Map preview</span>
                        <span
                            class="rounded-md bg-white/15 px-2 py-0.5 text-[0.65rem] font-semibold tracking-wide text-white uppercase backdrop-blur-sm">Maps
                            API</span>
                    </div>
                </div>
                <div class="flex flex-1 flex-col p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-navy-900/5 text-navy-700"
                                data-address-icon></span>
                            <div>
                                <h2 class="font-display text-lg font-bold text-navy-900" data-address-label-el></h2>
                                <p class="text-sm text-navy-500" data-address-name-el></p>
                            </div>
                        </div>
                    </div>
                    <address class="mt-4 not-italic text-sm leading-relaxed text-navy-700" data-address-text></address>
                    <p class="mt-3 text-sm text-navy-500" data-address-phone-el></p>
                    <div class="mt-auto flex flex-wrap items-center gap-2 pt-6">
                        <button type="button" data-address-set-default
                            class="rounded-xl px-3 py-2 text-xs font-semibold text-olive-700 transition-colors duration-200 hover:bg-olive-50">Set
                            as default</button>
                        <div class="ml-auto flex gap-2">
                            <button type="button" data-address-edit
                                class="inline-flex items-center gap-1.5 rounded-xl border border-navy-200 bg-surface px-3 py-2 text-xs font-semibold text-navy-900 transition-colors duration-200 hover:border-navy-300 hover:bg-navy-50">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    aria-hidden="true">
                                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                </svg>
                                Edit
                            </button>
                            <button type="button" data-address-delete
                                class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-surface px-3 py-2 text-xs font-semibold text-red-600 transition-colors duration-200 hover:border-red-300 hover:bg-red-50">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    aria-hidden="true">
                                    <path
                                        d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        </template>
    </div>

</x-layouts.app>
