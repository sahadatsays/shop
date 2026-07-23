<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Permission" />

    <x-admin.form-card title="Permission details">
        <form method="POST" action="{{ route('admin.permissions.update', $permission) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.permissions._form', ['permission' => $permission, 'groups' => $groups])
            <div class="flex gap-3">
                <x-admin.button type="submit">Save changes</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.permissions.show', $permission)">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.form-card>
</x-layouts.admin>
