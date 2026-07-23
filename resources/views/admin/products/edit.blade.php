<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit product" :description="'Update ' . $form->product->name">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.products.show', $form->product)">View</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    @include('admin.products._form', [
        'action' => route('admin.products.update', $form->product),
        'method' => 'PUT',
        'submitLabel' => 'Save changes',
    ])
</x-layouts.admin>
