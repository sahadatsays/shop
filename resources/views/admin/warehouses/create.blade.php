<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create warehouse" description="Add a fulfillment location for inventory tracking.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.warehouses.index')">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.warehouses.store') }}" class="space-y-6">
        @csrf
        @include('admin.warehouses._form')

        <div class="flex flex-wrap gap-2">
            <x-admin.button type="submit">Create warehouse</x-admin.button>
            <x-admin.button variant="secondary" :href="route('admin.warehouses.index')">Cancel</x-admin.button>
        </div>
    </form>
</x-layouts.admin>
