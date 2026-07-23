<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Feature" />
    @include('admin.homepage.features._form', ['action' => route('admin.homepage.features.store'), 'method' => 'POST', 'submitLabel' => 'Create feature'])
</x-layouts.admin>
