<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit customer" :description="'Update ' . $form->customer->name">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.customers.show', $form->customer)">View</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    @include('admin.customers._form', [
        'action' => route('admin.customers.update', $form->customer),
        'method' => 'PUT',
        'submitLabel' => 'Save changes',
    ])
</x-layouts.admin>
