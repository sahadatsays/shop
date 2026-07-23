<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header
        title="Notifications"
        description="Review order updates, inventory alerts, and system messages."
    >
        <x-slot:actions>
            <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
                @csrf
                <x-admin.button type="submit" variant="secondary" size="sm" :disabled="$unreadCount === 0">
                    Mark all read
                </x-admin.button>
            </form>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-6 flex flex-wrap gap-2">
        <x-admin.filter-tabs :tabs="[
            ['label' => 'All', 'href' => route('admin.notifications.index', array_filter(['category' => $filters['category'] ?? null])), 'active' => empty($filters['unread'])],
            ['label' => 'Unread', 'href' => route('admin.notifications.index', array_filter(['unread' => 1, 'category' => $filters['category'] ?? null])), 'active' => ! empty($filters['unread'])],
        ]" />
    </div>

    <div class="overflow-hidden rounded-[var(--radius-admin-lg)] border admin-border admin-surface shadow-sm">
        <div class="border-b admin-border bg-admin-bg/30 px-4 py-3.5 sm:px-6">
            <form method="GET" action="{{ route('admin.notifications.index') }}" class="flex flex-wrap gap-2">
                @if (! empty($filters['unread']))
                    <input type="hidden" name="unread" value="1">
                @endif
                <select name="category" class="rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 py-2.5 text-sm admin-text admin-focus-ring">
                    <option value="">All categories</option>
                    @foreach (\App\Enums\NotificationCategory::cases() as $category)
                        <option value="{{ $category->value }}" @selected(($filters['category'] ?? null) === $category->value)>
                            {{ $category->label() }}
                        </option>
                    @endforeach
                </select>
                <x-admin.button type="submit" variant="secondary" size="sm">Filter</x-admin.button>
            </form>
        </div>

        @if ($notifications->isEmpty())
            <x-admin.empty-state
                title="No notifications"
                description="You're all caught up. New alerts will appear here when orders change or system events occur."
            />
        @else
            <div class="divide-y admin-border">
                @foreach ($notifications as $notification)
                    <div class="flex items-start gap-4 px-4 py-4 sm:px-6 {{ $notification->isRead() ? '' : 'bg-admin-accent-muted/20' }}">
                        <span class="mt-2 size-2 shrink-0 rounded-full {{ $notification->isRead() ? 'bg-admin-muted' : 'bg-admin-brand' }}"></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="font-medium admin-text">{{ $notification->title }}</p>
                                    <p class="mt-1 text-sm admin-text-secondary">{{ $notification->body }}</p>
                                </div>
                                <time class="shrink-0 text-xs admin-muted">{{ $notification->created_at?->diffForHumans() }}</time>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <x-admin.badge :variant="$notification->isRead() ? 'muted' : 'brand'">
                                    {{ $notification->category->label() }}
                                </x-admin.badge>
                                @if ($notification->action_url)
                                    <a href="{{ $notification->action_url }}" class="text-sm font-medium text-admin-brand admin-focus-ring">
                                        {{ $notification->action_label ?? 'View details' }}
                                    </a>
                                @endif
                                @unless ($notification->isRead())
                                    <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-medium admin-muted admin-focus-ring hover:admin-text">
                                            Mark read
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t admin-border px-4 py-4 sm:px-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
