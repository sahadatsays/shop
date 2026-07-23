<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Roles" description="Manage admin roles and the permissions assigned to each team profile.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('access-matrix.manage'))
                <x-admin.button variant="secondary" size="sm" :href="route('admin.roles.matrix')">Permission matrix</x-admin.button>
            @endif
            @if (auth('admin')->user()?->hasPermission('roles.manage'))
                <x-admin.button :href="route('admin.roles.create')" size="sm">Add role</x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <form method="GET" action="{{ route('admin.roles.index') }}" class="flex w-full gap-2 lg:w-auto lg:ml-auto">
                <div class="relative min-w-0 flex-1 lg:w-72">
                    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search roles…"
                           class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 px-3 text-sm admin-text admin-focus-ring">
                </div>
                <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
            </form>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Role</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Permissions</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Users</th>
                <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($roles as $role)
                <tr class="hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.roles.show', $role) }}" class="font-medium admin-text hover:text-admin-brand">{{ $role->name }}</a>
                        <p class="text-xs admin-muted">{{ $role->slug }}</p>
                        @if ($role->is_system)
                            <x-admin.badge variant="muted" class="mt-2">System</x-admin.badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $role->permissions_count }}</td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $role->users_count }}</td>
                    <td class="px-6 py-4 text-right">
                        <x-admin.button variant="ghost" size="sm" :href="route('admin.roles.show', $role)">View</x-admin.button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-14"><x-admin.empty-state title="No roles found" /></td></tr>
            @endforelse
        </tbody>
        @if ($roles->hasPages())
            <x-slot:footer>{{ $roles->links() }}</x-slot:footer>
        @endif
    </x-admin.data-table>
</x-layouts.admin>
