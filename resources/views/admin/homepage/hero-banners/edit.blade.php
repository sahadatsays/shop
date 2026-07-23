<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Edit Hero Banner" description="Update slide content, images, and schedule." />
    @include('admin.homepage.hero-banners._form', ['banner' => $banner, 'action' => route('admin.homepage.hero-banners.update', $banner), 'method' => 'PUT', 'submitLabel' => 'Save changes'])
</x-layouts.admin>
