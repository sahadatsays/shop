<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @php
        $canManage = auth('admin')->user()?->hasPermission('dashboard.widgets.manage');
    @endphp

    <x-admin.page-header title="Dashboard Widgets" description="Create, enable, and configure the widgets available across every dashboard.">
        <x-slot:actions>
            @if ($canManage)
                <x-admin.button size="sm" :href="route('admin.dashboard-widgets.create')">
                    <svg class="mr-1.5 size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    New widget
                </x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    @if (session('status'))
        <div class="mt-4 rounded-[var(--radius-admin)] border border-admin-success/30 bg-admin-success/10 px-4 py-3 text-sm text-admin-success">
            {{ session('status') }}
        </div>
    @endif

    <x-admin.card class="mt-6" :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b admin-border bg-admin-bg/60 text-xs uppercase tracking-wide admin-muted">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium">Widget</th>
                        <th scope="col" class="hidden px-4 py-3 font-medium md:table-cell">Type</th>
                        <th scope="col" class="hidden px-4 py-3 font-medium lg:table-cell">Access</th>
                        <th scope="col" class="px-4 py-3 font-medium">Size</th>
                        <th scope="col" class="px-4 py-3 font-medium">Order</th>
                        <th scope="col" class="px-4 py-3 font-medium">Status</th>
                        <th scope="col" class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y admin-border">
                    @forelse ($widgets as $widget)
                        <tr class="admin-table-row hover:bg-admin-accent-muted/20">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($widget->icon)
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-admin-brand">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="{{ $widget->icon }}"/></svg>
                                        </span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-medium admin-text">{{ $widget->name }}</p>
                                        <p class="font-mono text-xs admin-muted">
                                            {{ $widget->key }}
                                            @if (! in_array($widget->key, $registeredKeys, true))
                                                <span class="text-admin-warning">· no provider</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden px-4 py-3 md:table-cell">
                                <x-admin.badge variant="muted">{{ $widget->type->label() }}</x-admin.badge>
                            </td>
                            <td class="hidden px-4 py-3 admin-text-secondary lg:table-cell">
                                <div class="text-xs">
                                    {{ $widget->permission ?? 'Any user' }}
                                    @if ($widget->roles->isNotEmpty())
                                        <span class="admin-muted">· {{ $widget->roles->pluck('name')->join(', ') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 admin-text-secondary">{{ $widget->width }}×{{ $widget->height }}</td>
                            <td class="px-4 py-3 admin-text-secondary">{{ $widget->display_order }}</td>
                            <td class="px-4 py-3">
                                <x-admin.badge :variant="$widget->is_active ? 'success' : 'muted'">{{ $widget->is_active ? 'Enabled' : 'Disabled' }}</x-admin.badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($canManage)
                                        <form method="POST" action="{{ route('admin.dashboard-widgets.toggle', $widget) }}">
                                            @csrf
                                            @method('PATCH')
                                            <x-admin.button type="submit" size="xs" variant="ghost">{{ $widget->is_active ? 'Disable' : 'Enable' }}</x-admin.button>
                                        </form>
                                        <x-admin.button size="xs" variant="secondary" :href="route('admin.dashboard-widgets.edit', $widget)">Edit</x-admin.button>
                                        @unless ($widget->is_system)
                                            <form method="POST" action="{{ route('admin.dashboard-widgets.destroy', $widget) }}" onsubmit="return confirm('Delete this widget?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-admin.button type="submit" size="xs" variant="danger-ghost">Delete</x-admin.button>
                                            </form>
                                        @endunless
                                    @else
                                        <span class="text-xs admin-muted">View only</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10">
                                <x-admin.empty-state title="No widgets defined" description="Run the dashboard widget seeder or create your first widget." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>
</x-layouts.admin>
