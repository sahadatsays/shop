<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Menus" description="Manage primary navigation and footer link columns." />

    <x-admin.data-table>
        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Menu</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Slug</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase admin-muted">Items</th>
                <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase admin-muted">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @foreach ($menus as $menu)
                <tr class="hover:bg-admin-bg/60">
                    <td class="px-6 py-4 font-medium admin-text">{{ $menu->name }}</td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $menu->slug }}</td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $menu->all_items_count }}</td>
                    <td class="px-6 py-4 text-right">
                        <x-admin.button variant="ghost" size="xs" :href="route('admin.homepage.menus.edit', $menu)">Manage items</x-admin.button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-admin.data-table>
</x-layouts.admin>
