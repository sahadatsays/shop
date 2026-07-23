<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Brand" description="Add a new brand to your catalog." />

    @include('admin.brands._form', [
        'form' => $form,
        'action' => route('admin.brands.store'),
        'method' => 'POST',
        'submitLabel' => 'Create brand',
    ])
</x-layouts.admin>
