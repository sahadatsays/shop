<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Promo Banner" />
    @include('admin.homepage.promo-banners._form', ['action' => route('admin.homepage.promo-banners.store'), 'method' => 'POST', 'submitLabel' => 'Create banner'])
</x-layouts.admin>
