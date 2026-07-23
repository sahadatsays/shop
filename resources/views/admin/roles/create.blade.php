<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Role" description="Define a new admin role and assign permissions." />

    <x-admin.form-card title="Role details">
        <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
            @csrf
            @include('admin.roles._form', ['permissions' => $permissions])
            <div class="flex gap-3">
                <x-admin.button type="submit">Create role</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.roles.index')">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.form-card>
</x-layouts.admin>
