<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create supplier" description="Add a vendor for purchasing and inventory replenishment.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.suppliers.index')">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-6">
        @csrf
        @include('admin.suppliers._form')

        <div class="flex flex-wrap gap-2">
            <x-admin.button type="submit">Create supplier</x-admin.button>
            <x-admin.button variant="secondary" :href="route('admin.suppliers.index')">Cancel</x-admin.button>
        </div>
    </form>
</x-layouts.admin>
