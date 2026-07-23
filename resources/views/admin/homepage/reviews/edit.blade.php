<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Review" />
    @include('admin.homepage.reviews._form', ['review' => $review, 'products' => $products, 'action' => route('admin.homepage.reviews.update', $review), 'method' => 'PUT', 'submitLabel' => 'Save changes'])
</x-layouts.admin>
