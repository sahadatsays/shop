@php
    $user = auth('admin')->user();
@endphp

<div class="relative">
    <button type="button"
            data-panel-trigger
            aria-controls="admin-profile-menu"
            aria-expanded="false"
            aria-haspopup="true"
            class="inline-flex items-center gap-2 rounded-[var(--radius-admin)] p-1.5 transition-colors duration-150 admin-focus-ring hover:bg-admin-accent-muted/60">
        <x-admin.avatar :name="$user?->name ?? 'Admin'" />
        <span class="hidden text-sm font-medium admin-text lg:block">{{ $user?->name ? explode(' ', $user->name)[0] : 'Admin' }}</span>
        <svg class="hidden size-4 admin-muted lg:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
    </button>

    <div id="admin-profile-menu"
         data-admin-panel
         hidden
         aria-hidden="true"
         role="menu"
         aria-label="Account menu"
         class="fixed inset-x-4 bottom-4 z-50 rounded-[var(--radius-admin-lg)] border admin-border admin-surface p-2 shadow-xl sm:absolute sm:inset-auto sm:right-0 sm:top-full sm:mt-2 sm:w-64">
        <div class="border-b admin-border px-3 py-3">
            <p class="text-sm font-semibold admin-text">{{ $user?->name ?? 'Admin User' }}</p>
            <p class="text-xs admin-muted">{{ $user?->email ?? 'admin@valorsupply.co' }}</p>
            <x-admin.badge variant="brand" class="mt-2">{{ $user?->displayRoleName() ?? 'Admin' }}</x-admin.badge>
        </div>
        <div class="py-1">
            @if ($user?->hasPermission('roles.view'))
                <a href="{{ route('admin.roles.index') }}" role="menuitem" class="block rounded-[var(--radius-admin)] px-3 py-2 text-sm transition-colors duration-150 admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 hover:admin-text">Roles & access</a>
            @endif
            @if ($user?->hasPermission('access-matrix.manage'))
                <a href="{{ route('admin.roles.matrix') }}" role="menuitem" class="block rounded-[var(--radius-admin)] px-3 py-2 text-sm transition-colors duration-150 admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 hover:admin-text">Permission matrix</a>
            @endif
        </div>
        <div class="border-t admin-border pt-1">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" role="menuitem" class="block w-full rounded-[var(--radius-admin)] px-3 py-2 text-left text-sm text-admin-danger transition-colors duration-150 admin-focus-ring hover:bg-red-50 dark:hover:bg-red-950/30">
                    Sign out
                </button>
            </form>
        </div>
    </div>
</div>
