<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Countdown" description="Add a time-limited promotion with a required end date." />

    @include('admin.promotions.banners._form', [
        'promotion' => null,
        'collections' => $collections,
        'offers' => $offers,
        'endsAtRequired' => true,
        'action' => route('admin.countdown-promotions.store'),
        'method' => 'POST',
        'submitLabel' => 'Create countdown',
        'cancelRoute' => route('admin.countdown-promotions.index'),
    ])
</x-layouts.admin>
