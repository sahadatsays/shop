@php
    $selectedRoles = collect(old('roles', isset($user) ? $user->roles->pluck('id')->all() : []))->map(fn ($id) => (int) $id);
    $isActive = old('is_active', $user->is_active ?? true);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3 dark:bg-red-950/20" role="alert">
        <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <x-admin.form-card title="Profile" description="Account identity and login credentials.">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.input
                    label="Full name"
                    name="name"
                    :value="old('name', $user->name ?? '')"
                    placeholder="Jordan Reeves"
                    required
                    class="sm:col-span-2"
                />

                <x-admin.input
                    label="Email"
                    name="email"
                    type="email"
                    :value="old('email', $user->email ?? '')"
                    placeholder="admin@valorsupply.co"
                    help="Used for admin panel sign-in."
                    required
                    class="sm:col-span-2"
                />

                <x-admin.input
                    label="{{ isset($user) ? 'New password' : 'Password' }}"
                    name="password"
                    type="password"
                    :required="! isset($user)"
                    help="{{ isset($user) ? 'Leave blank to keep the current password.' : 'Minimum 8 characters.' }}"
                    class="sm:col-span-2"
                />

                <x-admin.input
                    label="Confirm password"
                    name="password_confirmation"
                    type="password"
                    :required="! isset($user)"
                    class="sm:col-span-2"
                />
            </div>
        </x-admin.form-card>

        <x-admin.form-card title="Roles" description="Permissions are inherited from assigned roles.">
            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($roles as $role)
                    <label class="flex items-start gap-2 rounded-[var(--radius-admin)] px-2 py-1.5 hover:bg-admin-bg/60">
                        <input
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role->id }}"
                            @checked($selectedRoles->contains($role->id))
                            class="mt-0.5 size-4 rounded border admin-border accent-admin-brand admin-focus-ring"
                        >
                        <span>
                            <span class="block text-sm font-medium admin-text">{{ $role->name }}</span>
                            @if ($role->description)
                                <span class="block text-xs admin-muted">{{ $role->description }}</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
            @error('roles')
                <p class="mt-2 text-sm text-admin-danger">{{ $message }}</p>
            @enderror
        </x-admin.form-card>
    </div>

    <div class="space-y-6">
        <x-admin.form-card title="Access" description="Inactive users cannot sign in to the admin panel.">
            @if (isset($user) && $user->id === auth('admin')->id())
                <input type="hidden" name="is_active" value="1">
                <label class="flex items-center gap-3 opacity-60">
                    <input type="checkbox" checked disabled class="size-4 rounded border admin-border accent-admin-brand">
                    <span class="text-sm admin-text">Active account</span>
                </label>
                <p class="mt-2 text-xs admin-muted">You cannot deactivate your own account.</p>
            @else
                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked(filter_var($isActive, FILTER_VALIDATE_BOOLEAN))
                        class="size-4 rounded border admin-border accent-admin-brand admin-focus-ring"
                    >
                    <span class="text-sm admin-text">Active account</span>
                </label>
            @endif
        </x-admin.form-card>
    </div>
</div>
