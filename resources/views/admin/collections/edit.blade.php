<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Collection" :description="'Update '.$collection->name" />

    @include('admin.collections._form', [
        'collection' => $collection,
        'products' => $products,
        'action' => route('admin.collections.update', $collection),
        'method' => 'PUT',
        'submitLabel' => 'Save changes',
        'cancelRoute' => route('admin.collections.show', $collection),
    ])
</x-layouts.admin>
