<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit admin user" :description="$user->email">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.users.show', $user)">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.users._form', ['user' => $user, 'roles' => $roles])

        <div class="flex flex-wrap gap-2">
            <x-admin.button type="submit">Save changes</x-admin.button>
            <x-admin.button variant="secondary" :href="route('admin.users.show', $user)">Cancel</x-admin.button>
        </div>
    </form>
</x-layouts.admin>
