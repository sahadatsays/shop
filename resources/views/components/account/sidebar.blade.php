@props([
    'active' => 'Dashboard',
])

@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'account', 'badge' => null, 'icon' => 'M3 11 12 3l9 8M6 10v10h12V10'],
        [
            'label' => 'Orders',
            'route' => 'account.orders',
            'badge' => null,
            'icon' => 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7ZM9 10V6a3 3 0 0 1 6 0v4',
        ],
        [
            'label' => 'Wishlist',
            'route' => 'wishlist',
            'badge' => ($wishlistItemCount ?? 0) > 0 ? (string) ($wishlistItemCount ?? 0) : null,
            'icon' => 'M12 21s-7.5-4.7-9.5-9A5.5 5.5 0 0 1 12 6.5 5.5 5.5 0 0 1 21.5 12c-2 4.3-9.5 9-9.5 9Z',
        ],
        [
            'label' => 'Addresses',
            'route' => 'account.addresses',
            'badge' => null,
            'icon' => 'M12 21s-6-5.5-6-10a6 6 0 0 1 12 0c0 4.5-6 10-6 10Zm0-7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z',
        ],
        // ['label' => 'Reviews', 'route' => 'account.reviews', 'badge' => null, 'icon' => 'M12 2.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.3l-5.8 3.1 1.1-6.5L2.6 9.3l6.5-.9L12 2.5Z'],
        // ['label' => 'Downloads', 'route' => null, 'badge' => null, 'icon' => 'M12 3v12m0 0 4-4m-4 4-4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2'],
        // ['label' => 'Rewards', 'route' => 'account', 'badge' => null, 'icon' => 'M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 0 2.5 6.5L12 20l-2.5 1.5L12 15Z'],
        [
            'label' => 'Notifications',
            'route' => 'account.notifications',
            'badge' =>
                ($customerUnreadNotificationCount ?? 0) > 0 ? (string) ($customerUnreadNotificationCount ?? 0) : null,
            'icon' => 'M6 9a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6M10 19a2 2 0 0 0 4 0',
        ],
        [
            'label' => 'Settings',
            'route' => 'account.settings',
            'badge' => null,
            'icon' =>
                'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.5-3a7.5 7.5 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a7.6 7.6 0 0 0-2-1.2L14.6 3h-4l-.4 2.6a7.6 7.6 0 0 0-2 1.2l-2.4-1-2 3.4 2 1.6a7.7 7.7 0 0 0 0 2.4l-2 1.6 2 3.4 2.4-1a7.6 7.6 0 0 0 2 1.2l.4 2.6h4l.4-2.6a7.6 7.6 0 0 0 2-1.2l2.4 1 2-3.4-2-1.6c.06-.4.1-.8.1-1.2Z',
        ],
    ];
@endphp

<aside {{ $attributes->merge(['class' => 'lg:sticky lg:top-24 lg:self-start']) }}>
    {{-- Profile --}}
    <div class="flex items-center gap-4 rounded-card bg-navy-900 p-5 text-white">
        @if ($accountCustomer?->avatarUrl())
            <span
                class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-bronze-500 font-display text-lg font-bold"
                aria-hidden="true">
                <img src="{{ $accountCustomer->avatarUrl() }}" alt="" class="size-full object-cover">
            </span>
        @else
            <span
                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-bronze-500 font-display text-lg font-bold"
                aria-hidden="true">{{ $accountCustomer?->initials() ?? 'CU' }}</span>
        @endif
        <div class="min-w-0">
            <p class="truncate font-display font-bold">{{ $accountCustomer?->name ?? 'Guest' }}</p>
            <p class="truncate text-xs text-navy-200">{{ $accountCustomer?->email }}</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav aria-label="Account navigation" class="mt-4 rounded-card bg-surface p-3 shadow-soft">
        <ul class="flex gap-1 overflow-x-auto scrollbar-none lg:flex-col lg:overflow-visible">
            @foreach ($navItems as $item)
                @php $isActive = $item['label'] === $active; @endphp
                <li class="shrink-0 lg:shrink">
                    <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                        @if ($isActive) aria-current="page" @endif
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium whitespace-nowrap transition-colors duration-200
                           {{ $isActive ? 'bg-navy-900 text-white shadow-soft' : 'text-navy-700 hover:bg-navy-900/5 hover:text-navy-900' }}">
                        <svg class="size-4.5 shrink-0 {{ $isActive ? 'text-bronze-400' : 'text-navy-400' }}"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="{{ $item['icon'] }}" />
                        </svg>
                        {{ $item['label'] }}
                        @if ($item['badge'])
                            <span @if ($item['label'] === 'Notifications') data-nav-notifications-badge @endif
                                class="ml-auto flex size-5 items-center justify-center rounded-full bg-bronze-500 text-[0.65rem] font-bold text-white">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
            <li class="shrink-0 border-navy-100 lg:mt-2 lg:shrink lg:border-t lg:pt-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium whitespace-nowrap text-red-600 transition-colors duration-200 hover:bg-red-50">
                        <svg class="size-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3M16 17l5-5-5-5M21 12H9" />
                        </svg>
                        Sign out
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>
