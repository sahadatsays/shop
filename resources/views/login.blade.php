<x-layouts.auth title="Sign In"
    description="Sign in to your Jackpot BD LTD account — track orders, manage addresses, and shop premium veteran gear.">

    <div class="min-h-screen lg:grid lg:grid-cols-2" data-login>

        {{-- Lifestyle panel --}}
        <div class="relative hidden overflow-hidden bg-navy-950 lg:block">
            <img src="{{ $banner['image'] ?? asset('storage/login/login-banner.png') }}"
                alt="Premium custom packaging and printed essentials, crafted to elevate every brand presentation."
                class="absolute inset-0 size-full object-cover opacity-90" loading="eager" width="1400" height="933">
            <div class="absolute inset-0 bg-linear-to-t from-navy-950 via-navy-950/40 to-navy-950/20"
                aria-hidden="true"></div>
            <div class="absolute inset-0 bg-linear-to-r from-navy-950/30 to-transparent" aria-hidden="true"></div>

            <div class="relative flex h-full flex-col justify-between p-12 xl:p-16">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 self-start"
                    aria-label="{{ config('app.name') }} — Home">
                    <span
                        class="flex size-10 items-center justify-center rounded-xl bg-white/10 text-bronze-400 backdrop-blur-sm">
                        <svg class="size-5.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z" />
                        </svg>
                    </span>
                    <span class="font-display text-lg font-bold text-white">{{ config('app.name') }}</span>
                </a>

                <div class="max-w-md animate-fade-in-up">
                    <p class="text-sm font-semibold tracking-widest text-bronze-400 uppercase">{{ $banner['eyebrow'] }}
                    </p>
                    <blockquote class="mt-4 font-display text-3xl leading-snug font-bold text-white xl:text-4xl">
                        {{ $banner['headline'] }}
                    </blockquote>
                    <p class="mt-5 text-base leading-relaxed text-navy-200">
                        {{ $banner['description'] }}
                    </p>
                </div>

                <p class="text-xs text-navy-400">{{ $banner['bottom_text'] }}</p>
            </div>
        </div>

        {{-- Form panel --}}
        <div class="flex min-h-screen flex-col">
            {{-- Mobile hero --}}
            <div class="relative h-44 overflow-hidden lg:hidden">
                <img src="{{ $banner['image'] ?? asset('storage/login/login-banner.png') }}" alt=""
                    class="absolute inset-0 size-full object-cover" loading="eager" aria-hidden="true">
                <div class="absolute inset-0 bg-navy-950/60" aria-hidden="true"></div>
                <div class="relative flex h-full flex-col justify-between p-6">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 self-start"
                        aria-label="{{ config('app.name') }} — Home">
                        <span class="flex size-9 items-center justify-center rounded-xl bg-white/10 text-bronze-400">
                            <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path
                                    d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z" />
                            </svg>
                        </span>
                        <span class="font-display text-base font-bold text-white">{{ config('app.name') }}</span>
                    </a>
                    <p class="font-display text-lg font-bold text-white">Welcome back</p>
                </div>
            </div>

            <div class="flex flex-1 flex-col justify-center px-6 py-10 sm:px-12 lg:px-16 xl:px-24">
                <div class="mx-auto w-full max-w-md animate-fade-in">
                    <div class="hidden lg:block">
                        <h1 class="font-display text-3xl font-bold tracking-tight text-navy-900">Welcome back</h1>
                        <p class="mt-2 text-navy-600">Sign in to manage your orders, track deliveries, and access your
                            account.
                        </p>
                    </div>

                    <form class="mt-8 space-y-5 lg:mt-10" data-login-form method="POST"
                        action="{{ route('login.store') }}" novalidate>
                        @csrf
                        <x-ui.input name="email" type="email" label="Email address" autocomplete="email"
                            placeholder="you@example.com" required />

                        <div class="relative">
                            <x-ui.input name="password" type="password" label="Password" autocomplete="current-password"
                                placeholder="Enter your password" required />
                            <button type="button" data-toggle-password="password" aria-label="Show password"
                                class="absolute top-9 right-3 flex size-9 items-center justify-center rounded-lg text-navy-400 transition-colors duration-200 hover:text-navy-700">
                                <svg data-eye-open class="size-4.5" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg data-eye-closed class="size-4.5" hidden viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-8-10-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                                    <path d="M1 1l22 22" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <label class="flex cursor-pointer items-center gap-2.5">
                                <input type="checkbox" name="remember" checked
                                    class="size-4.5 rounded border-navy-300 accent-olive-600 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                <span class="text-sm text-navy-700">Remember me</span>
                            </label>
                            <a href="{{ route('password.forgot') }}"
                                class="text-sm font-medium text-bronze-600 underline-offset-4 transition-colors duration-200 hover:text-bronze-700 hover:underline">
                                Forgot password?
                            </a>
                        </div>

                        <x-ui.button type="submit" variant="primary" class="w-full" data-login-submit>
                            <span data-login-label>Sign in</span>
                        </x-ui.button>

                        <p class="text-center text-sm text-navy-500" data-login-status aria-live="polite"></p>
                    </form>

                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-navy-100"></div>
                        </div>
                        <p class="relative flex justify-center">
                            <span class="bg-surface px-4 text-xs font-medium tracking-wide text-navy-400 uppercase">Or
                                continue with</span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <button type="button" data-social-login="google"
                            class="inline-flex items-center justify-center gap-3 rounded-xl border border-navy-200 bg-surface px-4 py-3 text-sm font-semibold text-navy-900 shadow-soft transition-all duration-200 hover:border-navy-300 hover:bg-navy-50 active:scale-[0.98]">
                            <svg class="size-5" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="#4285F4"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1Z" />
                                <path fill="#34A853"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23Z" />
                                <path fill="#FBBC05"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18A10.96 10.96 0 0 0 1 12c0 1.77.42 3.45 1.18 4.93l3.66-2.84Z" />
                                <path fill="#EA4335"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53Z" />
                            </svg>
                            Google
                        </button>
                        <button type="button" data-social-login="apple"
                            class="inline-flex items-center justify-center gap-3 rounded-xl border border-navy-900 bg-navy-900 px-4 py-3 text-sm font-semibold text-white shadow-soft transition-all duration-200 hover:bg-navy-800 active:scale-[0.98]">
                            <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path
                                    d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09ZM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25Z" />
                            </svg>
                            Apple
                        </button>
                    </div>

                    <p class="mt-10 text-center text-sm text-navy-600">
                        New to Valor?
                        <a href="{{ route('register') }}"
                            class="font-semibold text-navy-900 underline-offset-4 transition-colors duration-200 hover:text-bronze-600 hover:underline">
                            Create an account
                        </a>
                    </p>

                    <p class="mt-8 text-center text-xs leading-relaxed text-navy-400">
                        By signing in, you agree to our
                        <a href="#" class="underline-offset-2 hover:text-navy-600 hover:underline">Terms</a>
                        and
                        <a href="#" class="underline-offset-2 hover:text-navy-600 hover:underline">Privacy
                            Policy</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>

</x-layouts.auth>
