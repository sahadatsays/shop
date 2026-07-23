<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Sign In' }} — {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (() => {
            const mode = localStorage.getItem('admin-theme') ?? 'system';
            const dark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    @vite(['resources/css/admin.css'])
</head>
<body class="min-h-screen font-sans antialiased admin-shell-bg admin-text">
    <div class="flex min-h-screen">
        <div class="relative hidden w-1/2 overflow-hidden lg:block">
            <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?w=1400&q=80&auto=format&fit=crop"
                 alt="Operations team reviewing fulfillment dashboards"
                 class="absolute inset-0 size-full object-cover opacity-80">
            <div class="absolute inset-0 bg-linear-to-t from-navy-950 via-navy-950/50 to-navy-900/30" aria-hidden="true"></div>
            <div class="relative flex h-full flex-col justify-between p-12">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-[var(--radius-admin)] bg-white/10 px-3 py-2 text-sm font-semibold text-white backdrop-blur-sm">
                        <svg class="size-4 text-bronze-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z"/>
                        </svg>
                        {{ config('app.name') }} Admin
                    </span>
                </div>
                <div class="max-w-lg">
                    <p class="text-sm font-semibold uppercase tracking-widest text-bronze-400">Secure administration</p>
                    <h1 class="mt-4 font-display text-4xl font-bold leading-tight text-white">Manage catalog, orders, and customer operations from one command center.</h1>
                    <p class="mt-4 text-base leading-relaxed text-navy-100">Role-based access keeps sensitive actions limited to the right team members.</p>
                </div>
                <p class="text-xs text-navy-300">Authorized personnel only. All sign-in activity is monitored.</p>
            </div>
        </div>

        <div class="flex w-full flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-16 xl:px-24">
            <div class="mx-auto w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <p class="text-sm font-semibold uppercase tracking-widest admin-muted">Administration</p>
                    <h1 class="mt-2 text-3xl font-bold admin-text">Sign in</h1>
                </div>

                <div class="hidden lg:block">
                    <h1 class="text-3xl font-bold admin-text">Admin sign in</h1>
                    <p class="mt-2 text-sm admin-text-secondary">Use your staff credentials to access the admin panel.</p>
                </div>

                @if ($errors->any())
                    <div class="mt-6 rounded-[var(--radius-admin)] border border-admin-danger/30 bg-red-50 px-4 py-3 text-sm text-admin-danger dark:bg-red-950/20">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <x-admin.input label="Email address" name="email" type="email" autocomplete="username" placeholder="owner@valorsupply.co" :value="old('email')" required />
                    <x-admin.input label="Password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required />

                    <label class="flex items-center gap-2.5">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="size-4 rounded border admin-border accent-admin-brand admin-focus-ring">
                        <span class="text-sm admin-text-secondary">Remember this device</span>
                    </label>

                    <x-admin.button type="submit" class="w-full">Sign in to admin</x-admin.button>
                </form>

                <p class="mt-8 text-center text-xs admin-muted">
                    Default owner login after seeding: <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono">owner@valorsupply.co</code> / <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono">password</code>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
