@php
    $notificationPrefs = [
        ['id' => 'notify-orders', 'label' => 'Order updates', 'desc' => 'Shipping, delivery, and return confirmations.', 'checked' => true],
        ['id' => 'notify-promotions', 'label' => 'Promotions & new arrivals', 'desc' => 'Sales, limited drops, and seasonal collections.', 'checked' => true],
        ['id' => 'notify-rewards', 'label' => 'Rewards & points', 'desc' => 'Tier changes, point balances, and redemption reminders.', 'checked' => true],
        ['id' => 'notify-reviews', 'label' => 'Review reminders', 'desc' => 'Prompts to review products after delivery.', 'checked' => false],
        ['id' => 'notify-newsletter', 'label' => 'Newsletter', 'desc' => 'Veteran stories, field guides, and brand news.', 'checked' => true],
    ];
@endphp

<x-layouts.app title="Profile Settings" description="Manage your Valor Supply Co. account — personal details, security, preferences, and notifications.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-profile>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">

            <x-account.sidebar active="Settings" />

            {{-- ============ Main ============ --}}
            <div class="lg:col-span-3">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                        <li><a href="{{ route('account') }}" class="transition-colors duration-200 hover:text-navy-900">Account</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="font-medium text-navy-900">Settings</li>
                    </ol>
                </nav>

                <div class="mt-4">
                    <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">Profile settings</h1>
                    <p class="mt-2 text-navy-600">Update your personal details, security, and how we keep in touch.</p>
                </div>

                <form class="mt-8 space-y-8" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate data-profile-form>
                    @csrf
                    @method('PUT')

                    {{-- Avatar + Personal information --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="personal-heading">
                        <h2 id="personal-heading" class="font-display text-lg font-bold text-navy-900">Personal information</h2>

                        {{-- Avatar upload --}}
                        <div class="mt-6 flex flex-col items-start gap-6 sm:flex-row sm:items-center">
                            <div class="relative">
                                @if ($customer->avatarUrl())
                                    <span data-avatar-preview class="flex size-24 items-center justify-center overflow-hidden rounded-full bg-bronze-500 font-display text-3xl font-bold text-white ring-4 ring-navy-100">
                                        <img src="{{ $customer->avatarUrl() }}" alt="{{ $customer->name }}" class="size-full object-cover">
                                    </span>
                                @else
                                    <span data-avatar-preview class="flex size-24 items-center justify-center overflow-hidden rounded-full bg-bronze-500 font-display text-3xl font-bold text-white ring-4 ring-navy-100">{{ $customer->initials() }}</span>
                                @endif
                                <label for="avatar" class="absolute -right-1 -bottom-1 flex size-9 cursor-pointer items-center justify-center rounded-full bg-navy-900 text-white shadow-soft transition-colors duration-200 hover:bg-navy-800">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/>
                                    </svg>
                                    <span class="sr-only">Upload avatar</span>
                                </label>
                                <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" class="sr-only" data-avatar-input>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-navy-900">Profile photo</p>
                                <p class="mt-1 text-sm text-navy-500">JPG, PNG, or WebP. Max 2 MB. Square works best.</p>
                                <button type="button" data-avatar-remove hidden
                                        class="mt-3 text-sm font-medium text-red-600 underline-offset-4 hover:underline">Remove photo</button>
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-ui.input name="first-name" label="First name" autocomplete="given-name" :value="$firstName" />
                            <x-ui.input name="last-name" label="Last name" autocomplete="family-name" :value="$lastName" />
                            <div class="sm:col-span-2">
                                <x-ui.input name="display-name" label="Display name" :value="$customer->name" hint="Shown on reviews and your public profile." readonly />
                            </div>
                        </div>
                    </section>

                    {{-- Email --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="email-heading">
                        <h2 id="email-heading" class="font-display text-lg font-bold text-navy-900">Email</h2>
                        <p class="mt-1 text-sm text-navy-500">Used for sign-in, receipts, and order updates.</p>
                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <x-ui.input name="email" type="email" label="Email address" autocomplete="email" :value="$customer->email" readonly />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="flex cursor-pointer items-center gap-3">
                                    <input type="checkbox" name="email-marketing" checked
                                           class="size-4.5 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                    <span class="text-sm text-navy-700">Send promotional emails to this address</span>
                                </label>
                            </div>
                        </div>
                    </section>

                    {{-- Phone --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="phone-heading">
                        <h2 id="phone-heading" class="font-display text-lg font-bold text-navy-900">Phone</h2>
                        <p class="mt-1 text-sm text-navy-500">For delivery questions and two-factor authentication.</p>
                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-ui.input name="phone" type="tel" label="Mobile number" autocomplete="tel" :value="$customer->phone" />
                            <div class="space-y-1.5">
                                <label for="phone-country" class="block text-sm font-medium text-navy-900">Country code</label>
                                <select id="phone-country" name="phone-country"
                                        class="block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                    <option value="+1" selected>United States (+1)</option>
                                    <option value="+1">Canada (+1)</option>
                                    <option value="+44">United Kingdom (+44)</option>
                                    <option value="+61">Australia (+61)</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="flex cursor-pointer items-center gap-3">
                                    <input type="checkbox" name="sms-updates" checked
                                           class="size-4.5 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                    <span class="text-sm text-navy-700">Send SMS delivery updates to this number</span>
                                </label>
                            </div>
                        </div>
                    </section>

                    {{-- Password --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="password-heading">
                        <h2 id="password-heading" class="font-display text-lg font-bold text-navy-900">Password</h2>
                        <p class="mt-1 text-sm text-navy-500">Last changed 3 months ago. Use a strong, unique password.</p>
                        <div class="mt-6 space-y-4">
                            <div class="relative">
                                <x-ui.input name="current-password" type="password" label="Current password" autocomplete="current-password" />
                                <button type="button" data-toggle-password="current-password" aria-label="Show current password"
                                        class="absolute top-9 right-3 flex size-8 items-center justify-center rounded-lg text-navy-400 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-700">
                                    <svg data-eye-open class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <svg data-eye-closed hidden class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.1A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a18.2 18.2 0 0 1-4.1 5.2M6.1 6.1C3.3 8 1 12 1 12s3.5 7 10 7c1.2 0 2.3-.2 3.4-.5"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="relative">
                                    <x-ui.input name="new-password" type="password" label="New password" autocomplete="new-password" hint="At least 8 characters." />
                                    <button type="button" data-toggle-password="new-password" aria-label="Show new password"
                                            class="absolute top-9 right-3 flex size-8 items-center justify-center rounded-lg text-navy-400 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-700">
                                        <svg data-eye-open class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg data-eye-closed hidden class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.1A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a18.2 18.2 0 0 1-4.1 5.2M6.1 6.1C3.3 8 1 12 1 12s3.5 7 10 7c1.2 0 2.3-.2 3.4-.5"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="relative">
                                    <x-ui.input name="confirm-password" type="password" label="Confirm new password" autocomplete="new-password" />
                                    <button type="button" data-toggle-password="confirm-password" aria-label="Show confirm password"
                                            class="absolute top-9 right-3 flex size-8 items-center justify-center rounded-lg text-navy-400 transition-colors duration-200 hover:bg-navy-50 hover:text-navy-700">
                                        <svg data-eye-open class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg data-eye-closed hidden class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.1A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a18.2 18.2 0 0 1-4.1 5.2M6.1 6.1C3.3 8 1 12 1 12s3.5 7 10 7c1.2 0 2.3-.2 3.4-.5"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Language & Currency --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="prefs-heading">
                        <h2 id="prefs-heading" class="font-display text-lg font-bold text-navy-900">Language &amp; currency</h2>
                        <p class="mt-1 text-sm text-navy-500">Set how prices and content appear across the store.</p>
                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="space-y-1.5">
                                <label for="language" class="block text-sm font-medium text-navy-900">Language</label>
                                <select id="language" name="language"
                                        class="block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                    <option value="en" selected>English (US)</option>
                                    <option value="en-gb">English (UK)</option>
                                    <option value="es">Español</option>
                                    <option value="fr">Français</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label for="currency" class="block text-sm font-medium text-navy-900">Currency</label>
                                <select id="currency" name="currency"
                                        class="block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                    <option value="USD" selected>USD — US Dollar ($)</option>
                                    <option value="CAD">CAD — Canadian Dollar (CA$)</option>
                                    <option value="GBP">GBP — British Pound (£)</option>
                                    <option value="EUR">EUR — Euro (€)</option>
                                    <option value="AUD">AUD — Australian Dollar (A$)</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    {{-- Notifications --}}
                    <section class="rounded-card bg-surface p-7 shadow-soft" aria-labelledby="notifications-heading">
                        <h2 id="notifications-heading" class="font-display text-lg font-bold text-navy-900">Notifications</h2>
                        <p class="mt-1 text-sm text-navy-500">Choose what we send and where. You can change this anytime.</p>
                        <ul class="mt-6 divide-y divide-navy-100">
                            @foreach ($notificationPrefs as $pref)
                                <li class="flex items-start justify-between gap-4 py-4 first:pt-0 last:pb-0">
                                    <div>
                                        <label for="{{ $pref['id'] }}" class="text-sm font-semibold text-navy-900">{{ $pref['label'] }}</label>
                                        <p class="mt-0.5 text-sm text-navy-500">{{ $pref['desc'] }}</p>
                                    </div>
                                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                                        <input type="checkbox" id="{{ $pref['id'] }}" name="{{ $pref['id'] }}" @checked($pref['checked']) class="peer sr-only">
                                        <span class="h-6 w-11 rounded-full bg-navy-200 transition-colors duration-200 peer-checked:bg-olive-600 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-bronze-500" aria-hidden="true"></span>
                                        <span class="absolute left-0.5 size-5 rounded-full bg-white shadow-soft transition-transform duration-200 peer-checked:translate-x-5" aria-hidden="true"></span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    {{-- Save --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 rounded-card bg-navy-50 p-6">
                        <p class="text-sm text-navy-600" data-save-status>Changes are saved when you press Save.</p>
                        <div class="flex flex-wrap gap-3">
                            <x-ui.button type="button" variant="ghost" data-profile-reset>Discard changes</x-ui.button>
                            <x-ui.button type="submit" variant="accent" data-profile-save>
                                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m5 13 4 4L19 7"/>
                                </svg>
                                <span data-save-label>Save changes</span>
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
