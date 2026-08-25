<x-layouts.auth title="Reset Password" description="Choose a new password for your Jackpot BD LTD account.">

    <div class="flex min-h-screen flex-col items-center justify-center px-6 py-12" data-reset-password>

        <a href="{{ route('home') }}" class="mb-10 inline-flex items-center gap-2.5"
            aria-label="{{ config('app.name') }} — Home">
            <span class="flex size-10 items-center justify-center rounded-xl bg-navy-900 text-bronze-400">
                <svg class="size-5.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path
                        d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z" />
                </svg>
            </span>
            <span class="font-display text-lg font-bold text-navy-900">{{ config('app.name') }}</span>
        </a>

        <div class="w-full max-w-md animate-fade-in">
            <div class="text-center">
                <h1 class="font-display text-3xl font-bold tracking-tight text-navy-900">Choose a new password</h1>
                <p class="mt-3 text-sm leading-relaxed text-navy-600">
                    Enter a strong password for your account below.
                </p>
            </div>

            <form class="mt-8 space-y-5" data-reset-form method="POST" action="{{ route('password.update') }}"
                novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <x-ui.input name="email" type="email" label="Email address" autocomplete="email" :value="old('email', $email)"
                    required />

                <div class="relative">
                    <x-ui.input name="password" type="password" label="New password" autocomplete="new-password"
                        placeholder="At least 8 characters" required />
                    <button type="button" data-toggle-password="password" aria-label="Show password"
                        class="absolute top-9 right-3 flex size-9 items-center justify-center rounded-lg text-navy-400 transition-colors duration-200 hover:text-navy-700">
                        <svg data-eye-open class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg data-eye-closed class="size-4.5" hidden viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true">
                            <path
                                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-8-10-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                            <path d="M1 1l22 22" />
                        </svg>
                    </button>
                </div>

                <x-ui.input name="password_confirmation" type="password" label="Confirm new password"
                    autocomplete="new-password" required />

                <x-ui.button type="submit" variant="primary" class="w-full" data-reset-submit>
                    <span data-reset-label>Reset password</span>
                </x-ui.button>

                <p class="text-center text-sm text-navy-500" data-reset-status aria-live="polite"></p>
            </form>

            <p class="mt-10 text-center">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-navy-700 transition-colors duration-200 hover:text-navy-900">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Back to sign in
                </a>
            </p>
        </div>
    </div>

</x-layouts.auth>
