@php
    use App\Support\Admin\Navigation\NavRegistry;

    $navItems = NavRegistry::sidebarFor(auth('admin')->user());
    $paletteGroups = NavRegistry::commandPalette();
    $quickActions = NavRegistry::quickActions();
    $currentRoute = request()->route()?->getName();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title . ' — Admin — ' . config('app.name') : 'Admin — ' . config('app.name') }}</title>
    <script>
        (() => {
            const mode = localStorage.getItem('admin-theme') ?? 'system';
            const dark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    @vite(['resources/css/admin.css', 'resources/js/admin/app.js'])
    <script>
        window.__adminPaletteGroups = @json($paletteGroups);
    </script>
</head>
<body class="min-h-screen font-sans antialiased admin-shell-bg admin-text">
    <a href="#admin-main"
       class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-[var(--radius-admin)] focus:bg-admin-accent focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white">
        Skip to main content
    </a>

    <div data-admin-shell
         data-sidebar-collapsed="false"
         data-sidebar-mobile-open="false"
         data-viewport="desktop"
         class="min-h-screen">

        <x-admin.sidebar :items="$navItems" :current-route="$currentRoute" />

        <div class="flex min-h-screen min-w-0 flex-col">
            <x-admin.topbar
                :breadcrumbs="$breadcrumbs ?? []"
                :page-title="$title ?? 'Dashboard'"
                :quick-actions="$quickActions"
            />

            <main id="admin-main" role="main" class="flex-1 overflow-x-hidden">
                <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <x-admin.page-loader />
    <x-admin.command-palette />
    <x-admin.toast-host />
    <x-admin.modal-host />

    <div data-admin-sidebar-backdrop hidden
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>
</body>
</html>
