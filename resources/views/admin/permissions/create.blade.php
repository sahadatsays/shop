<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Permission" />

    <x-admin.form-card title="Permission details">
        <form method="POST" action="{{ route('admin.permissions.store') }}" class="space-y-6">
            @csrf
            @include('admin.permissions._form', ['groups' => $groups])
            <div class="flex gap-3">
                <x-admin.button type="submit">Create permission</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.permissions.index')">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.form-card>
</x-layouts.admin>
