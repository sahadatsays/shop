<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Category" description="Add a new category or subcategory to your catalog." />

    @include('admin.categories._form', [
        'form' => $form,
        'action' => route('admin.categories.store'),
        'method' => 'POST',
        'submitLabel' => 'Create category',
    ])
</x-layouts.admin>
