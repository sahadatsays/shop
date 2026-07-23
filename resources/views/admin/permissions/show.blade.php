<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header :title="$permission->name" />

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Overview">
                <dl>
                    <x-admin.detail-row label="Name" :value="$permission->name" />
                    <x-admin.detail-row label="Slug"><code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $permission->slug }}</code></x-admin.detail-row>
                    <x-admin.detail-row label="Group" :value="$permission->group" />
                    <x-admin.detail-row label="Description" :value="$permission->description" />
                </dl>
            </x-admin.form-card>
        </div>
        <div class="space-y-6">
            <x-admin.form-card title="Roles with access">
                <div class="flex flex-wrap gap-2">
                    @forelse ($permission->roles as $role)
                        <x-admin.badge variant="brand">{{ $role->name }}</x-admin.badge>
                    @empty
                        <p class="text-sm admin-muted">Not assigned to any role.</p>
                    @endforelse
                </div>
            </x-admin.form-card>
            @if (auth('admin')->user()?->hasPermission('permissions.manage'))
                <x-admin.button :href="route('admin.permissions.edit', $permission)">Edit permission</x-admin.button>
            @endif
        </div>
    </div>
</x-layouts.admin>
