<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Feature" />
    @include('admin.homepage.features._form', ['feature' => $feature, 'action' => route('admin.homepage.features.update', $feature), 'method' => 'PUT', 'submitLabel' => 'Save changes'])
</x-layouts.admin>
