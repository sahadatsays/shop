<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Banner" :description="'Update '.$promotion->name" />

    @include('admin.promotions.banners._form', [
        'promotion' => $promotion,
        'collections' => $collections,
        'offers' => $offers,
        'action' => route('admin.banner-promotions.update', $promotion),
        'method' => 'PUT',
        'submitLabel' => 'Save changes',
        'cancelRoute' => route('admin.banner-promotions.index'),
    ])
</x-layouts.admin>
