<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => window.adminToast?.push({ title: @json(session('success')), type: 'success' }));</script>
    @endif

    <x-admin.page-header title="Permissions" description="Manage granular admin capabilities grouped by module.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('permissions.manage'))
                <x-admin.button :href="route('admin.permissions.create')" size="sm">Add permission</x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <form method="GET" action="{{ route('admin.permissions.index') }}" class="flex w-full flex-col gap-2 sm:flex-row lg:ml-auto lg:w-auto">
                <select name="group" class="rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 py-2.5 text-sm admin-text admin-focus-ring">
                    <option value="">All groups</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group }}" @selected(($filters['group'] ?? null) === $group)>{{ $group }}</option>
                    @endforeach
                </select>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search permissions…"
                       class="rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 py-2.5 text-sm admin-text admin-focus-ring">
                <x-admin.button type="submit" variant="secondary" size="sm">Filter</x-admin.button>
            </form>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Permission</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Group</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Roles</th>
                <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($permissions as $permission)
                <tr class="hover:bg-admin-bg/60">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.permissions.show', $permission) }}" class="font-medium admin-text hover:text-admin-brand">{{ $permission->name }}</a>
                        <p class="text-xs admin-muted">{{ $permission->slug }}</p>
                    </td>
                    <td class="px-6 py-4"><x-admin.badge variant="muted">{{ $permission->group }}</x-admin.badge></td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $permission->roles_count }}</td>
                    <td class="px-6 py-4 text-right">
                        <x-admin.button variant="ghost" size="sm" :href="route('admin.permissions.show', $permission)">View</x-admin.button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-14"><x-admin.empty-state title="No permissions found" /></td></tr>
            @endforelse
        </tbody>
        @if ($permissions->hasPages())
            <x-slot:footer>{{ $permissions->links() }}</x-slot:footer>
        @endif
    </x-admin.data-table>
</x-layouts.admin>
