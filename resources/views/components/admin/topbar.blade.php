@props([
    'breadcrumbs' => [],
    'pageTitle' => 'Dashboard',
    'quickActions' => [],
])

<header role="banner" class="admin-topbar-glass sticky top-0 z-30 border-b admin-border">
    <div class="flex h-[var(--topbar-height)] items-center gap-3 px-4 sm:px-6 lg:px-8">
        {{-- Mobile menu --}}
        <button type="button"
                data-sidebar-mobile-toggle
                aria-expanded="false"
                aria-label="Open navigation menu"
                class="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-admin)] admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 lg:hidden">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Breadcrumb / title --}}
        <div class="min-w-0 flex-1">
            <div class="hidden md:block">
                <x-admin.breadcrumbs :items="$breadcrumbs" />
            </div>
            <p class="truncate text-sm font-semibold admin-text md:hidden">{{ $pageTitle }}</p>
        </div>

        {{-- Search --}}
        <button type="button"
                data-palette-open
                class="hidden min-w-[12rem] flex-1 items-center gap-2 rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3 py-2 text-left text-sm admin-muted admin-focus-ring hover:admin-text-secondary sm:flex lg:max-w-xs xl:max-w-sm">
            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/>
            </svg>
            <span class="truncate">Search…</span>
            <kbd class="ml-auto hidden rounded border admin-border px-1.5 py-0.5 text-[10px] font-medium admin-muted lg:inline">⌘K</kbd>
        </button>

        <button type="button"
                data-palette-open
                aria-label="Open command palette"
                class="inline-flex size-11 items-center justify-center rounded-[var(--radius-admin)] admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 sm:hidden">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/>
            </svg>
        </button>

        <div class="flex shrink-0 items-center gap-1 sm:gap-2">
            <x-admin.quick-actions :actions="$quickActions" />
            <x-admin.notification-panel />
            <x-admin.theme-toggle />
            <x-admin.profile-menu />
        </div>
    </div>
</header>
