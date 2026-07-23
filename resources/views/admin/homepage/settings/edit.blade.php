<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Homepage Settings" description="Control section visibility, product limits, SEO, and popular searches." />

    @include('admin.homepage.settings._form', [
        'settings' => $settings,
        'sectionLabels' => $sectionLabels,
        'enabledSections' => $enabledSections,
        'action' => route('admin.homepage.settings.update'),
        'method' => 'PUT',
    ])
</x-layouts.admin>
