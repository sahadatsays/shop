@php
    $user = auth('admin')->user();
@endphp

<div class="relative">
    <button type="button" data-panel-trigger aria-controls="admin-profile-menu" aria-expanded="false" aria-haspopup="true"
        class="inline-flex items-center gap-2 rounded-[var(--radius-admin)] p-1.5 transition-colors duration-150 admin-focus-ring hover:bg-admin-accent-muted/60">
        <x-admin.avatar :name="$user?->name ?? 'Admin'" />
        <span
            class="hidden text-sm font-medium admin-text lg:block">{{ $user?->name ? explode(' ', $user->name)[0] : 'Admin' }}</span>
        <svg class="hidden size-4 admin-muted lg:block" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" aria-hidden="true">
            <path d="m6 9 6 6 6-6" />
        </svg>
    </button>

    <div id="admin-profile-menu" data-admin-panel hidden aria-hidden="true" role="menu" aria-label="Account menu"
        class="fixed inset-x-4 bottom-4 z-50 rounded-[var(--radius-admin-lg)] border admin-border admin-surface p-2 shadow-xl sm:absolute sm:inset-auto sm:right-0 sm:top-full sm:mt-2 sm:w-64">
        {{-- Clickable name/email → own profile --}}
        <a href="{{ $user ? route('admin.users.show', $user->id) : '#' }}" role="menuitem"
            class="block rounded-[var(--radius-admin)] px-3 py-3 transition-colors duration-150 admin-focus-ring hover:bg-admin-accent-muted/60">
            <p class="text-sm font-semibold admin-text">{{ $user?->name ?? 'Admin User' }}</p>
            <p class="text-xs admin-muted">{{ $user?->email ?? '' }}</p>
            <x-admin.badge variant="brand" class="mt-2">{{ $user?->displayRoleName() ?? 'Admin' }}</x-admin.badge>
        </a>

        <div class="border-t admin-border mt-1 py-1">
            {{-- Visit website --}}
            <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" role="menuitem"
                class="flex items-center gap-2 rounded-[var(--radius-admin)] px-3 py-2 text-sm transition-colors duration-150 admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 hover:admin-text">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" />
                    <path
                        d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                </svg>
                Visit website
            </a>

            {{-- Change password --}}
            {{-- @if ($user)
                <a href="{{ route('admin.users.edit', $user->id) }}"
                   role="menuitem"
                   class="flex items-center gap-2 rounded-[var(--radius-admin)] px-3 py-2 text-sm transition-colors duration-150 admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 hover:admin-text">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Change password
                </a>
            @endif --}}
        </div>

        <div class="border-t admin-border pt-1">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" role="menuitem"
                    class="flex w-full items-center gap-2 rounded-[var(--radius-admin)] px-3 py-2 text-left text-sm text-admin-danger transition-colors duration-150 admin-focus-ring hover:bg-red-50 dark:hover:bg-red-950/30">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" />
                    </svg>
                    Sign out
                </button>
            </form>
        </div>
    </div>
</div>
