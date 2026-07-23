<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))<script>document.addEventListener('DOMContentLoaded', () => window.adminToast?.push({ title: @json(session('success')), type: 'success' }));</script>@endif

    <x-admin.page-header :title="$menu->name" description="Manage links for this menu. Use parent items for mega menu groups." />

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-4 xl:col-span-2">
            <x-admin.form-card title="Menu items">
                <div class="space-y-3">
                    @forelse ($topLevelItems as $item)
                        <div class="rounded-[var(--radius-admin)] border admin-border p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium admin-text">{{ $item->label }}</p>
                                    <p class="text-xs admin-muted">{{ $item->route_name ? 'Route: '.$item->route_name : $item->url }}</p>
                                    @if ($item->children->isNotEmpty())
                                        <ul class="mt-2 space-y-1 border-l-2 admin-border pl-3">
                                            @foreach ($item->children as $child)
                                                <li class="text-sm admin-text-secondary">{{ $child->label }} — {{ $child->route_name ?: $child->url }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                @if (auth('admin')->user()?->hasPermission('homepage.manage'))
                                    <form method="POST" action="{{ route('admin.homepage.menus.items.destroy', [$menu, $item]) }}" onsubmit="return confirm('Delete this item and its children?')">
                                        @csrf @method('DELETE')
                                        <x-admin.button type="submit" variant="danger-ghost" size="xs">Delete</x-admin.button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <x-admin.empty-state title="No menu items yet" description="Add your first link using the form." />
                    @endforelse
                </div>
            </x-admin.form-card>
        </div>

        @if (auth('admin')->user()?->hasPermission('homepage.manage'))
            <x-admin.form-card title="Add menu item">
                <form method="POST" action="{{ route('admin.homepage.menus.items.store', $menu) }}" class="space-y-4">
                    @csrf
                    <x-admin.input label="Label" name="label" required />
                    <x-admin.select label="Parent item" name="parent_id">
                        <option value="">None (top-level)</option>
                        @foreach ($topLevelItems as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->label }}</option>
                        @endforeach
                    </x-admin.select>
                    <x-admin.select label="Route name" name="route_name">
                        <option value="">Custom URL</option>
                        @foreach ($routeOptions as $routeName)
                            <option value="{{ $routeName }}">{{ $routeName }}</option>
                        @endforeach
                    </x-admin.select>
                    <x-admin.input label="URL" name="url" placeholder="/shop or https://..." />
                    <x-admin.input label="Sort order" name="sort_order" type="number" min="0" value="0" />
                    <x-admin.checkbox label="Open in new tab" name="open_in_new_tab" />
                    <x-admin.checkbox label="External link" name="is_external" />
                    <x-admin.checkbox label="Active" name="is_active" :checked="true" />
                    <x-admin.button type="submit" class="w-full">Add item</x-admin.button>
                </form>
            </x-admin.form-card>
        @endif
    </div>
</x-layouts.admin>
