<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Category" :description="'Update '.$form->category->name" />

    @include('admin.categories._form', [
        'form' => $form,
        'action' => route('admin.categories.update', $form->category),
        'method' => 'PUT',
        'submitLabel' => 'Save changes',
    ])
</x-layouts.admin>
