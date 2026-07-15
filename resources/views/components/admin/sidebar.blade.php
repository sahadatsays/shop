@props([
    'items' => [],
    'currentRoute' => null,
])

<aside data-admin-sidebar
       aria-label="Admin navigation"
       aria-hidden="false"
       class="fixed inset-y-0 left-0 z-50 flex w-[min(18rem,100vw)] flex-col border-r admin-border admin-surface transition-transform duration-200 ease-out lg:sticky lg:top-0 lg:z-auto lg:h-screen lg:w-auto lg:translate-x-0 [[data-sidebar-mobile-open=false]_&]:-translate-x-full lg:[[data-sidebar-mobile-open=false]_&]:translate-x-0">

    {{-- Brand --}}
    <div class="flex h-[var(--topbar-height)] shrink-0 items-center gap-3 border-b admin-border px-4 [[data-sidebar-collapsed=true]_&]:lg:justify-center [[data-sidebar-collapsed=true]_&]:lg:px-2">
        <span class="flex size-9 shrink-0 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent text-admin-brand">
            <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z"/>
            </svg>
        </span>
        <div class="min-w-0 [[data-sidebar-collapsed=true]_&]:lg:hidden">
            <p class="truncate text-sm font-semibold admin-text">{{ config('app.name') }}</p>
            <p class="truncate text-xs admin-muted">Admin</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="admin-scrollbar flex-1 overflow-y-auto px-3 py-4" aria-label="Modules">
        <ul class="space-y-1">
            @foreach ($items as $item)
                @if ($item->children)
                    <x-admin.nav-group :item="$item" :current-route="$currentRoute" />
                @else
                    <li>
                        <x-admin.nav-item :item="$item" :current-route="$currentRoute" />
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>

    {{-- Footer --}}
    <div class="shrink-0 border-t admin-border p-3">
        <button type="button"
                data-sidebar-collapse
                aria-expanded="true"
                aria-label="Collapse sidebar"
                class="flex w-full items-center justify-center gap-2 rounded-[var(--radius-admin)] px-3 py-2.5 text-sm font-medium admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 [[data-sidebar-collapsed=true]_&]:lg:px-2">
            <svg class="size-4 shrink-0 transition-transform [[data-sidebar-collapsed=true]_&]:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="m15 18-6-6 6-6"/>
            </svg>
            <span class="[[data-sidebar-collapsed=true]_&]:lg:hidden">Collapse</span>
        </button>
        <p class="mt-2 text-center text-[10px] admin-muted [[data-sidebar-collapsed=true]_&]:lg:hidden">v1.0 · Shell</p>
    </div>
</aside>
