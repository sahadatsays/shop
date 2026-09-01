<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit warehouse" :description="$warehouse->code">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.warehouses.show', $warehouse)">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.warehouses.update', $warehouse) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.warehouses._form', ['warehouse' => $warehouse])

        <div class="flex flex-wrap gap-2">
            <x-admin.button type="submit">Save changes</x-admin.button>
            <x-admin.button variant="secondary" :href="route('admin.warehouses.show', $warehouse)">Cancel</x-admin.button>
        </div>
    </form>
</x-layouts.admin>
