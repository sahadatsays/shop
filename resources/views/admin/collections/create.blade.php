<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Collection" description="Curate a product group for marketing placements." />

    @include('admin.collections._form', [
        'products' => $products,
        'action' => route('admin.collections.store'),
        'method' => 'POST',
        'submitLabel' => 'Create collection',
        'cancelRoute' => route('admin.collections.index'),
    ])
</x-layouts.admin>
