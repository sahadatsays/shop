<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create customer" description="Add a customer profile with addresses and notes.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.customers.index')">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    @include('admin.customers._form', [
        'action' => route('admin.customers.store'),
        'method' => 'POST',
        'submitLabel' => 'Create customer',
    ])
</x-layouts.admin>
