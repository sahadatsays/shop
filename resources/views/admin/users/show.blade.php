<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header :title="$user->name" description="Admin account details, roles, and effective permissions." />

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Overview">
                <dl>
                    <x-admin.detail-row label="Name" :value="$user->name" />
                    <x-admin.detail-row label="Email" :value="$user->email" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$user->is_active ? 'success' : 'muted'" dot>{{ $user->is_active ? 'Active' : 'Inactive' }}</x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Last login" :value="$user->last_login_at?->format('M j, Y g:i A') ?? 'Never'" />
                    <x-admin.detail-row label="Joined" :value="$user->created_at?->format('M j, Y')" />
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="Roles">
                <div class="flex flex-wrap gap-2">
                    @forelse ($user->roles as $role)
                        <a href="{{ route('admin.roles.show', $role) }}" class="inline-flex">
                            <x-admin.badge variant="brand">{{ $role->name }}</x-admin.badge>
                        </a>
                    @empty
                        <p class="text-sm admin-muted">No roles assigned.</p>
                    @endforelse
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Effective permissions">
                <div class="flex flex-wrap gap-2">
                    @if ($user->hasRole('owner'))
                        <x-admin.badge variant="brand">Full access (owner)</x-admin.badge>
                    @else
                        @php
                            $permissions = $user->roles
                                ->flatMap(fn ($role) => $role->permissions)
                                ->unique('id')
                                ->sortBy('group')
                                ->values();
                        @endphp
                        @forelse ($permissions as $permission)
                            <x-admin.badge variant="muted">{{ $permission->name }}</x-admin.badge>
                        @empty
                            <p class="text-sm admin-muted">No permissions via assigned roles.</p>
                        @endforelse
                    @endif
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            @if (auth('admin')->user()?->hasPermission('users.manage'))
                <div class="flex flex-col gap-2">
                    <x-admin.button :href="route('admin.users.edit', $user)">Edit user</x-admin.button>
                    @if ($user->id !== auth('admin')->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this admin user permanently?')">
                            @csrf
                            @method('DELETE')
                            <x-admin.button type="submit" variant="danger-ghost" class="w-full">Delete user</x-admin.button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
