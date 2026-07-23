<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance — {{ $storeSettings->displayName() ?? config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-navy-950 px-4 text-navy-100">
    <main class="max-w-lg text-center">
        <span class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-white/10 text-bronze-400">
            <svg class="size-8" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z"/>
            </svg>
        </span>

        <h1 class="mt-8 font-display text-3xl font-bold text-white">{{ $storeSettings->displayName() ?? config('app.name') }}</h1>

        <p class="mt-4 text-lg leading-relaxed text-navy-200">
            {{ $storeSettings->maintenance_message ?? 'We are performing scheduled maintenance and will be back shortly. Thank you for your patience.' }}
        </p>

        <p class="mt-8 text-sm text-navy-400">Please check back soon.</p>
    </main>
</body>
</html>
