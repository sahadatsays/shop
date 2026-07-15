@props([
    'items' => [],
])

<nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="admin-muted admin-focus-ring rounded hover:admin-text">Admin</a>

    @foreach ($items as $item)
        <span class="admin-muted" aria-hidden="true">/</span>
        @if ($loop->last || empty($item['href']))
            <span class="truncate font-medium admin-text" aria-current="page">{{ $item['label'] }}</span>
        @else
            <a href="{{ $item['href'] }}" class="truncate admin-muted admin-focus-ring rounded hover:admin-text">{{ $item['label'] }}</a>
        @endif
    @endforeach
</nav>
