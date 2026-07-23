<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header title="Permission Matrix" description="Review and update role permissions across the admin platform.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.roles.index')">Back to roles</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.roles.matrix.update') }}">
        @csrf
        @method('PUT')

        <x-admin.form-card title="Access matrix" description="Checked boxes grant the permission to that role. Owner always has full access.">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b admin-border bg-admin-bg/40">
                            <th class="sticky left-0 z-10 bg-admin-bg/95 px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Permission</th>
                            @foreach ($roles as $role)
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider admin-muted">
                                    <span class="block">{{ $role->name }}</span>
                                    <span class="mt-1 block font-normal normal-case admin-muted">{{ $role->slug }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-admin-border/60">
                        @foreach ($groupedPermissions as $group => $items)
                            <tr class="bg-admin-bg/30">
                                <td colspan="{{ $roles->count() + 1 }}" class="px-4 py-2 text-xs font-semibold uppercase tracking-wider admin-muted">{{ $group }}</td>
                            </tr>
                            @foreach ($items as $permission)
                                <tr>
                                    <td class="sticky left-0 z-10 bg-admin-surface px-4 py-3">
                                        <p class="font-medium admin-text">{{ $permission->name }}</p>
                                        <p class="text-xs admin-muted">{{ $permission->slug }}</p>
                                    </td>
                                    @foreach ($roles as $role)
                                        <td class="px-4 py-3 text-center">
                                            @if ($role->slug === 'owner')
                                                <x-admin.badge variant="success">All</x-admin.badge>
                                            @else
                                                <input type="checkbox"
                                                       name="matrix[{{ $role->id }}][{{ $permission->id }}]"
                                                       value="1"
                                                       @checked($matrix[$role->id][$permission->id] ?? false)
                                                       class="size-4 rounded border admin-border accent-admin-brand admin-focus-ring">
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <x-admin.button type="submit">Save matrix</x-admin.button>
            </div>
        </x-admin.form-card>
    </form>
</x-layouts.admin>
