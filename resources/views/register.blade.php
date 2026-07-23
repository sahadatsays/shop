<x-layouts.auth title="Create Account" description="Create your Valor Supply Co. account — shop premium veteran gear, track orders, and join our community.">

    <div class="min-h-screen lg:grid lg:grid-cols-2" data-register>

        {{-- Lifestyle panel --}}
        <div class="relative hidden overflow-hidden bg-navy-950 lg:block">
            <img src="https://images.unsplash.com/photo-1529156069898-49953e39b684?w=1400&q=80&auto=format&fit=crop"
                 alt="Friends gathered outdoors sharing a moment together"
                 class="absolute inset-0 size-full object-cover opacity-90"
                 loading="eager" width="1400" height="933">
            <div class="absolute inset-0 bg-linear-to-t from-navy-950 via-navy-950/50 to-navy-950/20" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-linear-to-r from-navy-950/30 to-transparent" aria-hidden="true"></div>

            <div class="relative flex h-full flex-col justify-between p-12 xl:p-16">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 self-start" aria-label="{{ config('app.name') }} — Home">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-white/10 text-bronze-400 backdrop-blur-sm">
                        <svg class="size-5.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z"/>
                        </svg>
                    </span>
                    <span class="font-display text-lg font-bold text-white">{{ config('app.name') }}</span>
                </a>

                <div class="max-w-md animate-fade-in-up">
                    <p class="text-sm font-semibold tracking-widest text-bronze-400 uppercase">Join the community</p>
                    <blockquote class="mt-4 font-display text-3xl leading-snug font-bold text-white xl:text-4xl">
                        Your account. Your orders. One place for everything Valor.
                    </blockquote>
                    <ul class="mt-6 space-y-3 text-sm text-navy-200">
                        <li class="flex items-center gap-3">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-bronze-500/20 text-bronze-400">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                            </span>
                            Faster checkout with saved addresses
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-bronze-500/20 text-bronze-400">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                            </span>
                            Order tracking and exclusive member offers
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-bronze-500/20 text-bronze-400">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
                            </span>
                            Rewards on every purchase
                        </li>
                    </ul>
                </div>

                <p class="text-xs text-navy-400">Free to join · No spam · Unsubscribe anytime</p>
            </div>
        </div>

        {{-- Form panel --}}
        <div class="flex min-h-screen flex-col">
            {{-- Mobile hero --}}
            <div class="relative h-40 overflow-hidden lg:hidden">
                <img src="https://images.unsplash.com/photo-1529156069898-49953e39b684?w=800&q=70&auto=format&fit=crop"
                     alt="" class="absolute inset-0 size-full object-cover" loading="eager" aria-hidden="true">
                <div class="absolute inset-0 bg-navy-950/60" aria-hidden="true"></div>
                <div class="relative flex h-full flex-col justify-between p-6">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 self-start" aria-label="{{ config('app.name') }} — Home">
                        <span class="flex size-9 items-center justify-center rounded-xl bg-white/10 text-bronze-400">
                            <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z"/>
                            </svg>
                        </span>
                        <span class="font-display text-base font-bold text-white">{{ config('app.name') }}</span>
                    </a>
                    <p class="font-display text-lg font-bold text-white">Create your account</p>
                </div>
            </div>

            <div class="flex flex-1 flex-col justify-center px-6 py-10 sm:px-12 lg:px-16 xl:px-24">
                <div class="mx-auto w-full max-w-md animate-fade-in">
                    <div class="hidden lg:block">
                        <h1 class="font-display text-3xl font-bold tracking-tight text-navy-900">Create your account</h1>
                        <p class="mt-2 text-navy-600">A few details and you’re ready to shop, track, and earn rewards.</p>
                    </div>

                    <form class="mt-8 space-y-4 lg:mt-10" data-register-form method="POST" action="{{ route('register.store') }}" novalidate>
                        @csrf
                        <x-ui.input name="name" label="Full name" autocomplete="name" placeholder="James Mitchell" required />

                        <x-ui.input name="email" type="email" label="Email address" autocomplete="email" placeholder="you@example.com" required />

                        <x-ui.input name="phone" type="tel" label="Phone number" autocomplete="tel" placeholder="+1 (555) 000-0000" hint="For delivery updates and order support." required />

                        <div class="relative">
                            <x-ui.input name="password" type="password" label="Password" autocomplete="new-password" placeholder="At least 8 characters" required />
                            <button type="button" data-toggle-password="password" aria-label="Show password"
                                    class="absolute top-9 right-3 flex size-9 items-center justify-center rounded-lg text-navy-400 transition-colors duration-200 hover:text-navy-700">
                                <svg data-eye-open class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg data-eye-closed class="size-4.5" hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-8-10-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/>
                                </svg>
                            </button>
                        </div>

                        <div class="relative">
                            <x-ui.input name="password-confirm" type="password" label="Confirm password" autocomplete="new-password" placeholder="Re-enter your password" required />
                            <button type="button" data-toggle-password="password-confirm" aria-label="Show confirm password"
                                    class="absolute top-9 right-3 flex size-9 items-center justify-center rounded-lg text-navy-400 transition-colors duration-200 hover:text-navy-700">
                                <svg data-eye-open class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg data-eye-closed class="size-4.5" hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-8-10-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/>
                                </svg>
                            </button>
                            <p class="mt-1.5 hidden text-sm text-red-600" data-password-mismatch role="alert">Passwords do not match.</p>
                        </div>

                        <div class="space-y-3 pt-2">
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="checkbox" name="newsletter"
                                       class="mt-0.5 size-4.5 shrink-0 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                <span class="text-sm leading-relaxed text-navy-700">
                                    Send me the Valor newsletter — veteran stories, new arrivals, and member-only offers.
                                </span>
                            </label>

                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="checkbox" name="terms" required data-terms-checkbox
                                       class="mt-0.5 size-4.5 shrink-0 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                <span class="text-sm leading-relaxed text-navy-700">
                                    I agree to the
                                    <a href="#" class="font-medium text-navy-900 underline-offset-2 hover:text-bronze-600 hover:underline">Terms of Service</a>
                                    and
                                    <a href="#" class="font-medium text-navy-900 underline-offset-2 hover:text-bronze-600 hover:underline">Privacy Policy</a>.
                                </span>
                            </label>
                        </div>

                        <x-ui.button type="submit" variant="primary" class="mt-2 w-full" data-register-submit>
                            <span data-register-label>Create account</span>
                        </x-ui.button>

                        <p class="text-center text-sm text-navy-500" data-register-status aria-live="polite"></p>
                    </form>

                    <p class="mt-8 text-center text-sm text-navy-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-navy-900 underline-offset-4 transition-colors duration-200 hover:text-bronze-600 hover:underline">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</x-layouts.auth>
