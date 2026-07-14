<x-layouts.auth title="Create Account" description="Create your Valor Supply Co. account — shop premium veteran gear and track your orders.">

    <div class="flex min-h-screen flex-col items-center justify-center px-6 py-16">
        <a href="{{ route('home') }}" class="mb-10 inline-flex items-center gap-2.5" aria-label="{{ config('app.name') }} — Home">
            <span class="flex size-10 items-center justify-center rounded-xl bg-navy-900 text-bronze-400">
                <svg class="size-5.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Zm0 4.2 1.4 2.84 3.13.46-2.26 2.2.53 3.12L12 13.35l-2.8 1.47.53-3.12-2.26-2.2 3.13-.46L12 6.2Z"/>
                </svg>
            </span>
            <span class="font-display text-lg font-bold text-navy-900">{{ config('app.name') }}</span>
        </a>

        <div class="w-full max-w-md rounded-card bg-surface p-8 text-center shadow-soft sm:p-10">
            <h1 class="font-display text-2xl font-bold text-navy-900">Create an account</h1>
            <p class="mt-2 text-sm text-navy-600">Registration is coming soon. Sign in with an existing account for now.</p>
            <x-ui.button :href="route('login')" variant="primary" class="mt-8 w-full">Back to sign in</x-ui.button>
        </div>
    </div>

</x-layouts.auth>
