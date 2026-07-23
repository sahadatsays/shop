<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Banner" description="Add a hero or storefront banner promotion." />

    @include('admin.promotions.banners._form', [
        'promotion' => null,
        'collections' => $collections,
        'offers' => $offers,
        'action' => route('admin.banner-promotions.store'),
        'method' => 'POST',
        'submitLabel' => 'Create banner',
        'cancelRoute' => route('admin.banner-promotions.index'),
    ])
</x-layouts.admin>
