<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header
        title="Store Settings"
        description="Configure storefront identity, contact details, regional preferences, email, maintenance mode, SEO, and theme colors."
    />

    @include('admin.settings._form', [
        'settings' => $settings,
        'currencies' => $currencies,
        'timezones' => $timezones,
        'themeColorFields' => $themeColorFields,
        'action' => route('admin.settings.update'),
        'method' => 'PUT',
    ])
</x-layouts.admin>
