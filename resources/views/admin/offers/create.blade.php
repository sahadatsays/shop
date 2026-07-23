<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Offer" description="Build a promotional event with products and optional discount." />

    @include('admin.offers._form', [
        'discounts' => $discounts,
        'products' => $products,
        'action' => route('admin.offers.store'),
        'method' => 'POST',
        'submitLabel' => 'Create offer',
        'cancelRoute' => route('admin.offers.index'),
    ])
</x-layouts.admin>
