<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Review" />
    @include('admin.homepage.reviews._form', ['products' => $products, 'action' => route('admin.homepage.reviews.store'), 'method' => 'POST', 'submitLabel' => 'Create review'])
</x-layouts.admin>
