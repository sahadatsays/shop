<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Discount" description="Add a new coupon or order discount." />

    @include('admin.discounts._form', [
        'action' => route('admin.discounts.store'),
        'method' => 'POST',
        'submitLabel' => 'Create discount',
    ])
</x-layouts.admin>
