<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create widget" description="Add a new widget to the dashboard catalog." />

    <form method="POST" action="{{ route('admin.dashboard-widgets.store') }}" class="mt-6">
        @csrf
        @include('admin.dashboard-widgets._form', ['submitLabel' => 'Create widget'])
    </form>
</x-layouts.admin>
