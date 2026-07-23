<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Offer" :description="'Update '.$offer->name" />

    @include('admin.offers._form', [
        'offer' => $offer,
        'discounts' => $discounts,
        'products' => $products,
        'action' => route('admin.offers.update', $offer),
        'method' => 'PUT',
        'submitLabel' => 'Save changes',
        'cancelRoute' => route('admin.offers.show', $offer),
    ])
</x-layouts.admin>
