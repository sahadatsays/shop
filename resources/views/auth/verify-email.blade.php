<x-layouts.app :title="$title ?? 'Verify email'"
    description="Confirm your email address to access your Jackpot BD LTD account.">

    <div class="mx-auto max-w-lg px-4 py-16 sm:px-6 lg:py-24">
        <div class="rounded-card bg-surface p-8 shadow-soft sm:p-10">
            <h1 class="font-display text-3xl font-bold text-navy-900">Verify your email</h1>
            <p class="mt-3 text-navy-600">
                We sent a verification link to
                <span class="font-semibold text-navy-900">{{ auth('customer')->user()?->email }}</span>.
                Open that email and confirm before using account features.
            </p>

            @if (session('success'))
                <p class="mt-6 rounded-field border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}</p>
            @endif

            @if (session('error'))
                <p class="mt-6 rounded-field border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}</p>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mt-8">
                @csrf
                <x-ui.button type="submit" variant="primary" class="w-full">Resend verification email</x-ui.button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full text-sm font-medium text-navy-500 hover:text-navy-900">
                    Sign out
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
