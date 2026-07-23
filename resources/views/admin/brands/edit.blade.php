<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Brand" :description="'Update '.$form->brand->name" />

    @include('admin.brands._form', [
        'form' => $form,
        'action' => route('admin.brands.update', $form->brand),
        'method' => 'PUT',
        'submitLabel' => 'Save changes',
    ])
</x-layouts.admin>
