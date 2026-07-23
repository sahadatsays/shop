<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Discount" :description="'Update '.$discount->name" />

    @include('admin.discounts._form', [
        'discount' => $discount,
        'action' => route('admin.discounts.update', $discount),
        'method' => 'PUT',
        'submitLabel' => 'Save changes',
    ])
</x-layouts.admin>
