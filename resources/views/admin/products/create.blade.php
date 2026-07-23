<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create product" description="Add a new product to your catalog.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.products.index')">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    @include('admin.products._form', [
        'action' => route('admin.products.store'),
        'method' => 'POST',
        'submitLabel' => 'Create product',
    ])
</x-layouts.admin>
