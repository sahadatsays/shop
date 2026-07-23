<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Promo Banner" />
    @include('admin.homepage.promo-banners._form', ['banner' => $banner, 'action' => route('admin.homepage.promo-banners.update', $banner), 'method' => 'PUT', 'submitLabel' => 'Save changes'])
</x-layouts.admin>
