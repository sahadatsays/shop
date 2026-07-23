<x-layouts.auth title="Reset Password" description="Reset your Valor Supply Co. account password — we'll send you a secure link by email.">

    <div class="flex min-h-screen flex-col items-center justify-center px-6 py-12" data-forgot-password>

        <a href="{{ route('home') }}" class="mb-10 inline-flex items-center gap-2.5" aria-label="{{ config('app.name') }} — Home">
            <span class="flex size-10 items-center justify-center rounded-xl bg-navy-900 text-bronze-400">
                <svg class="size-5.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z"/>
                </svg>
            </span>
            <span class="font-display text-lg font-bold text-navy-900">{{ config('app.name') }}</span>
        </a>

        <div class="w-full max-w-md animate-fade-in">
            {{-- Request state --}}
            <div data-forgot-request>
                {{-- Illustration --}}
                <div class="mx-auto flex size-32 items-center justify-center rounded-full bg-navy-900/5" aria-hidden="true">
                    <svg viewBox="0 0 120 120" class="size-24" fill="none" role="img" aria-label="Password reset illustration">
                        <circle cx="60" cy="60" r="52" class="stroke-navy-100" stroke-width="2"/>
                        <rect x="32" y="44" width="56" height="40" rx="6" class="fill-surface stroke-navy-200" stroke-width="2"/>
                        <path d="M32 50h56" class="stroke-navy-200" stroke-width="2"/>
                        <path d="M60 44V34a12 12 0 0 0-12 12" class="stroke-bronze-500" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="60" cy="64" r="4" class="fill-bronze-500"/>
                        <path d="M60 68v6" class="stroke-bronze-500" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M44 78c4 4 8 6 16 6s12-2 16-6" class="stroke-navy-300" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="3 4"/>
                    </svg>
                </div>

                <div class="mt-8 text-center">
                    <h1 class="font-display text-3xl font-bold tracking-tight text-navy-900">Forgot your password?</h1>
                    <p class="mt-3 text-sm leading-relaxed text-navy-600">
                        No worries — enter the email on your account and we’ll send a secure link to reset your password.
                    </p>
                </div>

                <form class="mt-8 space-y-5" data-forgot-form method="POST" action="{{ route('password.email') }}" novalidate>
                    @csrf
                    <x-ui.input name="email" type="email" label="Email address" autocomplete="email" placeholder="you@example.com" required />

                    <x-ui.button type="submit" variant="primary" class="w-full" data-forgot-submit>
                        <span data-forgot-label>Send reset link</span>
                    </x-ui.button>

                    <p class="text-center text-sm text-navy-500" data-forgot-status aria-live="polite"></p>
                </form>
            </div>

            {{-- Success state --}}
            <div data-forgot-success hidden class="text-center">
                <div class="mx-auto flex size-32 items-center justify-center rounded-full bg-olive-50" aria-hidden="true">
                    <svg viewBox="0 0 120 120" class="size-24" fill="none" role="img" aria-label="Email sent successfully">
                        <circle cx="60" cy="60" r="52" class="stroke-olive-200" stroke-width="2"/>
                        <rect x="28" y="38" width="64" height="44" rx="6" class="fill-surface stroke-olive-300" stroke-width="2"/>
                        <path d="M28 44l32 22 32-22" class="stroke-olive-500" stroke-width="2" stroke-linejoin="round"/>
                        <circle cx="84" cy="76" r="16" class="fill-olive-600"/>
                        <path d="M77 76l5 5 10-10" class="stroke-white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h2 class="mt-8 font-display text-3xl font-bold tracking-tight text-navy-900">Check your email</h2>
                <p class="mt-3 text-sm leading-relaxed text-navy-600">
                    We sent a password reset link to
                    <strong class="font-medium text-navy-900" data-forgot-email-display>you@example.com</strong>.
                    The link expires in 60 minutes.
                </p>

                <div class="mt-8 rounded-card border border-navy-100 bg-canvas p-5 text-left text-sm text-navy-600">
                    <p class="font-medium text-navy-900">Didn’t receive it?</p>
                    <ul class="mt-2 space-y-1.5 list-disc pl-4">
                        <li>Check your spam or promotions folder</li>
                        <li>Make sure you entered the correct email</li>
                        <li>Wait a few minutes and try again</li>
                    </ul>
                </div>

                <button type="button" data-forgot-resend
                        class="mt-6 text-sm font-semibold text-bronze-600 underline-offset-4 transition-colors duration-200 hover:text-bronze-700 hover:underline">
                    Resend reset link
                </button>

                <p class="mt-4 text-sm text-green-700" data-forgot-resend-status aria-live="polite"></p>
            </div>

            <p class="mt-10 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-navy-700 transition-colors duration-200 hover:text-navy-900">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                    Back to sign in
                </a>
            </p>
        </div>
    </div>

</x-layouts.auth>
