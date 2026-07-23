<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header :title="'Edit: '.$widget->name" description="Update this widget's configuration and access rules." />

    <form method="POST" action="{{ route('admin.dashboard-widgets.update', $widget) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('admin.dashboard-widgets._form', ['submitLabel' => 'Update widget'])
    </form>
</x-layouts.admin>
