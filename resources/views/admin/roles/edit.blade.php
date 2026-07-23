<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Role" :description="'Update permissions for ' . $role->name" />

    <x-admin.form-card title="Role details">
        <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.roles._form', ['role' => $role, 'permissions' => $permissions])
            <div class="flex gap-3">
                <x-admin.button type="submit">Save changes</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.roles.show', $role)">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.form-card>
</x-layouts.admin>
