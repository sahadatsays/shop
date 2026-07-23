<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header :title="$role->name" description="Role details, assigned permissions, and linked admin users." />

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Overview">
                <dl>
                    <x-admin.detail-row label="Name" :value="$role->name" />
                    <x-admin.detail-row label="Slug"><code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $role->slug }}</code></x-admin.detail-row>
                    <x-admin.detail-row label="Description" :value="$role->description" />
                    <x-admin.detail-row label="Type">
                        <x-admin.badge :variant="$role->is_system ? 'muted' : 'brand'">{{ $role->is_system ? 'System' : 'Custom' }}</x-admin.badge>
                    </x-admin.detail-row>
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="Permissions">
                <div class="flex flex-wrap gap-2">
                    @forelse ($role->permissions as $permission)
                        <x-admin.badge variant="brand">{{ $permission->name }}</x-admin.badge>
                    @empty
                        <p class="text-sm admin-muted">No permissions assigned.</p>
                    @endforelse
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Assigned users">
                <ul class="space-y-3">
                    @forelse ($role->users as $user)
                        <li class="text-sm admin-text">{{ $user->name }} <span class="admin-muted">· {{ $user->email }}</span></li>
                    @empty
                        <li class="text-sm admin-muted">No users assigned.</li>
                    @endforelse
                </ul>
            </x-admin.form-card>

            @if (auth('admin')->user()?->hasPermission('roles.manage'))
                <div class="flex flex-col gap-2">
                    <x-admin.button :href="route('admin.roles.edit', $role)">Edit role</x-admin.button>
                    @unless ($role->is_system)
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">
                            @csrf
                            @method('DELETE')
                            <x-admin.button type="submit" variant="danger-ghost" class="w-full">Delete role</x-admin.button>
                        </form>
                    @endunless
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
