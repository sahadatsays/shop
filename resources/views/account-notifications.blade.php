@php
    use App\Enums\NotificationCategory;

    $filters = ['All', 'Orders', 'Promotions', 'Account'];

    $iconPaths = [
        'package' =>
            'M12 3 3 7.5 12 12l9-4.5L12 3Zm0 9L3 10.5M12 12v9M21 10.5V17a2 2 0 0 1-1 1.7l-7 3.5a2 2 0 0 1-1.8 0l-7-3.5A2 2 0 0 1 3 17v-6.5',
        'tag' => 'M3 7h6l7 7-3 3-7-7V7Zm4 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z',
        'shield' => 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z',
        'alert' => 'M12 9v4m0 4h.01M10.3 3.6 2.6 18a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.6a2 2 0 0 0-3.4 0Z',
    ];

    $iconColors = [
        'orders' => 'bg-navy-900/5 text-navy-700',
        'promotions' => 'bg-bronze-500/10 text-bronze-700',
        'account' => 'bg-navy-100 text-navy-600',
    ];

    $filterKey = strtolower($activeFilter ?? 'all');
@endphp

<x-layouts.app title="Notifications"
    description="Your Jackpot BD LTD notification center — orders, offers, rewards, and account updates in one place.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14" data-notifications
        data-mark-all-url="{{ route('account.notifications.mark-all-read') }}">

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">

            <x-account.sidebar active="Notifications" />

            <div class="lg:col-span-3">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                        <li><a href="{{ route('account') }}"
                                class="transition-colors duration-200 hover:text-navy-900">Account</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="font-medium text-navy-900">Notifications</li>
                    </ol>
                </nav>

                <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">Notifications</h1>
                        @if ($unreadCount > 0)
                            <span data-notifications-unread-badge
                                class="flex h-6 min-w-6 items-center justify-center rounded-full bg-bronze-500 px-2 text-xs font-bold text-white">
                                {{ $unreadCount }}
                            </span>
                        @else
                            <span data-notifications-unread-badge hidden
                                class="flex h-6 min-w-6 items-center justify-center rounded-full bg-bronze-500 px-2 text-xs font-bold text-white">0</span>
                        @endif
                    </div>
                    <button type="button" data-notifications-mark-all
                        class="inline-flex items-center gap-2 rounded-xl border border-navy-200 bg-surface px-4 py-2 text-sm font-semibold text-navy-900 shadow-soft transition-colors duration-200 hover:border-navy-300 hover:bg-navy-50 disabled:cursor-not-allowed disabled:opacity-40"
                        @if ($unreadCount === 0) disabled @endif>
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m5 13 4 4L19 7" />
                        </svg>
                        Mark all read
                    </button>
                </div>

                <p class="mt-2 text-navy-600" data-notifications-summary aria-live="polite">
                    {{ $unreadCount }} unread · {{ $totalCount }} total
                </p>

                <div class="mt-6 flex flex-wrap gap-2" role="group" aria-label="Filter notifications">
                    @foreach ($filters as $filter)
                        @php
                            $isActive =
                                strtolower($filter) === $filterKey || ($filter === 'All' && $filterKey === 'all');
                            $href =
                                $filter === 'All'
                                    ? route('account.notifications')
                                    : route('account.notifications', ['filter' => strtolower($filter)]);
                        @endphp
                        <a href="{{ $href }}" data-notifications-filter="{{ $filter }}"
                            aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                            class="rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 {{ $isActive ? 'bg-navy-900 text-white shadow-soft' : 'bg-surface text-navy-700 shadow-soft hover:bg-navy-900/5' }}">
                            {{ $filter }}
                        </a>
                    @endforeach
                </div>

                <div class="mt-8 space-y-10" data-notifications-list>
                    @forelse ($groups as $groupLabel => $items)
                        <section data-notification-group data-group-label="{{ $groupLabel }}">
                            <h2 class="font-display text-sm font-bold tracking-wide text-navy-500 uppercase">
                                {{ $groupLabel }}</h2>

                            <div class="relative mt-4 ml-3 border-l-2 border-navy-100 pl-8">
                                @foreach ($items as $notification)
                                    @php
                                        $type = $notification->category->customerType();
                                        $icon = match ($notification->category) {
                                            NotificationCategory::OrderUpdate => 'package',
                                            NotificationCategory::Promotion => 'tag',
                                            default => 'shield',
                                        };
                                    @endphp
                                    <article data-notification data-notification-id="{{ $notification->id }}"
                                        data-mark-read-url="{{ route('account.notifications.read', $notification) }}"
                                        data-type="{{ $type }}"
                                        data-read="{{ $notification->isRead() ? 'true' : 'false' }}" tabindex="0"
                                        class="group relative pb-6 last:pb-0 {{ !$notification->isRead() ? 'is-unread' : '' }}">
                                        <span data-notification-dot
                                            class="absolute -left-[calc(2rem+5px)] top-5 flex size-2.5 rounded-full ring-4 ring-canvas transition-colors duration-200 {{ $notification->isRead() ? 'bg-navy-200' : 'bg-bronze-500' }}"
                                            aria-hidden="true"></span>

                                        <div data-notification-card
                                            class="rounded-card border bg-surface p-5 shadow-soft transition-all duration-200 {{ $notification->isRead() ? 'border-navy-100' : 'border-bronze-200/60 bg-bronze-50/30' }} hover:shadow-card">
                                            <div class="flex gap-4">
                                                <span
                                                    class="flex size-11 shrink-0 items-center justify-center rounded-xl {{ $iconColors[$type] ?? $iconColors['account'] }}">
                                                    <svg class="size-5" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                                        stroke-linejoin="round" aria-hidden="true">
                                                        <path d="{{ $iconPaths[$icon] }}" />
                                                    </svg>
                                                </span>

                                                <div class="min-w-0 flex-1">
                                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                                        <div class="flex items-center gap-2">
                                                            @unless ($notification->isRead())
                                                                <span data-unread-indicator
                                                                    class="size-2 shrink-0 rounded-full bg-bronze-500"
                                                                    aria-label="Unread"></span>
                                                            @endunless
                                                            <h3
                                                                class="font-display text-base font-semibold text-navy-900">
                                                                {{ $notification->title }}</h3>
                                                        </div>
                                                        <time
                                                            class="shrink-0 text-xs text-navy-400">{{ $notification->timeAgo() }}</time>
                                                    </div>

                                                    <p class="mt-1.5 text-sm leading-relaxed text-navy-600">
                                                        {{ $notification->body }}</p>

                                                    <div class="mt-3 flex flex-wrap items-center gap-3">
                                                        <x-ui.badge :variant="$notification->category->badgeVariant()">
                                                            {{ $notification->category->customerFilterLabel() }}
                                                        </x-ui.badge>

                                                        @if ($notification->action_url)
                                                            <a href="{{ $notification->action_url }}"
                                                                class="text-sm font-semibold text-bronze-600 underline-offset-4 transition-colors duration-200 hover:text-bronze-700 hover:underline">
                                                                {{ $notification->action_label ?? 'View details' }}
                                                            </a>
                                                        @endif

                                                        @unless ($notification->isRead())
                                                            <button type="button" data-notification-mark-read
                                                                class="ml-auto text-xs font-semibold text-navy-500 transition-colors duration-200 hover:text-navy-900">
                                                                Mark read
                                                            </button>
                                                        @endunless
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @empty
                        <div class="rounded-card border border-navy-100 bg-surface p-12 text-center shadow-soft">
                            <h2 class="font-display text-xl font-bold text-navy-900">No notifications yet</h2>
                            <p class="mx-auto mt-2 max-w-sm text-sm text-navy-600">Order updates, promotions, and
                                account alerts will show up here.</p>
                        </div>
                    @endforelse
                </div>

                <div data-notifications-filter-empty hidden
                    class="mt-8 rounded-card border border-navy-100 bg-surface p-12 text-center shadow-soft">
                    <span
                        class="mx-auto flex size-16 items-center justify-center rounded-full bg-navy-900/5 text-navy-500">
                        <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 9a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6M10 19a2 2 0 0 0 4 0" />
                        </svg>
                    </span>
                    <h2 class="mt-5 font-display text-xl font-bold text-navy-900">No notifications here</h2>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-navy-600">Try a different filter or check back later
                        for updates.</p>
                    <a href="{{ route('account.notifications') }}"
                        class="mt-6 inline-block text-sm font-semibold text-bronze-600 underline-offset-4 hover:underline">Show
                        all notifications</a>
                </div>

                <div data-notifications-all-read @if ($unreadCount > 0) hidden @endif
                    class="mt-8 rounded-card border border-olive-200/60 bg-olive-50/40 p-6 text-center shadow-soft">
                    <p class="flex items-center justify-center gap-2 text-sm font-medium text-olive-800">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m5 13 4 4L19 7" />
                        </svg>
                        You’re all caught up
                    </p>
                </div>

                <p class="mt-8 text-sm text-navy-500" data-notifications-status aria-live="polite"></p>
            </div>
        </div>
    </div>

</x-layouts.app>
