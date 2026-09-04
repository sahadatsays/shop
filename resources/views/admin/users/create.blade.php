<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create admin user" description="Add a staff account and assign one or more roles.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.users.index')">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf
        @include('admin.users._form', ['roles' => $roles])

        <div class="flex flex-wrap gap-2">
            <x-admin.button type="submit">Create admin user</x-admin.button>
            <x-admin.button variant="secondary" :href="route('admin.users.index')">Cancel</x-admin.button>
        </div>
    </form>
</x-layouts.admin>
