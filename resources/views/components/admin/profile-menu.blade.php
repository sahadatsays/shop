<div class="relative">
    <button type="button"
            data-panel-trigger
            aria-controls="admin-profile-menu"
            aria-expanded="false"
            aria-haspopup="true"
            class="inline-flex items-center gap-2 rounded-[var(--radius-admin)] p-1.5 transition-colors duration-150 admin-focus-ring hover:bg-admin-accent-muted/60">
        <x-admin.avatar name="Jordan Reeves" />
        <span class="hidden text-sm font-medium admin-text lg:block">Jordan</span>
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
            <p class="text-sm font-semibold admin-text">Jordan Reeves</p>
            <p class="text-xs admin-muted">jordan@valorsupply.co</p>
            <x-admin.badge variant="brand" class="mt-2">Super Admin</x-admin.badge>
        </div>
        <div class="py-1">
            <a href="#" role="menuitem" class="block rounded-[var(--radius-admin)] px-3 py-2 text-sm transition-colors duration-150 admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 hover:admin-text">Profile</a>
            <a href="#" role="menuitem" class="block rounded-[var(--radius-admin)] px-3 py-2 text-sm transition-colors duration-150 admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 hover:admin-text">Preferences</a>
            <a href="#" role="menuitem" class="block rounded-[var(--radius-admin)] px-3 py-2 text-sm transition-colors duration-150 admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 hover:admin-text">Audit log</a>
        </div>
        <div class="border-t admin-border pt-1">
            <a href="{{ route('home') }}" role="menuitem" class="block rounded-[var(--radius-admin)] px-3 py-2 text-sm text-admin-danger transition-colors duration-150 admin-focus-ring hover:bg-red-50 dark:hover:bg-red-950/30">Sign out</a>
        </div>
    </div>
</div>
