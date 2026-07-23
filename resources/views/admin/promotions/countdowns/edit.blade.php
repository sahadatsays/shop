<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Countdown" :description="'Update '.$promotion->name" />

    @include('admin.promotions.banners._form', [
        'promotion' => $promotion,
        'collections' => $collections,
        'offers' => $offers,
        'endsAtRequired' => true,
        'action' => route('admin.countdown-promotions.update', $promotion),
        'method' => 'PUT',
        'submitLabel' => 'Save changes',
        'cancelRoute' => route('admin.countdown-promotions.index'),
    ])
</x-layouts.admin>
