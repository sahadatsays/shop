<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Create Hero Banner" description="Add a new slide to the homepage hero slider." />
    @include('admin.homepage.hero-banners._form', ['action' => route('admin.homepage.hero-banners.store'), 'method' => 'POST', 'submitLabel' => 'Create banner'])
</x-layouts.admin>
