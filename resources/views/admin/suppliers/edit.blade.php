<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit supplier" :description="$supplier->company_name ?: $supplier->name">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.suppliers.show', $supplier)">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.suppliers._form', ['supplier' => $supplier])

        <div class="flex flex-wrap gap-2">
            <x-admin.button type="submit">Save changes</x-admin.button>
            <x-admin.button variant="secondary" :href="route('admin.suppliers.show', $supplier)">Cancel</x-admin.button>
        </div>
    </form>
</x-layouts.admin>
