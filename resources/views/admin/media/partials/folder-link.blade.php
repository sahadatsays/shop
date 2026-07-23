@php
    $isActive = ($currentFolder ?? null) === $folder->id;
    $query = array_filter([
        'folder_id' => $folder->id,
        'search' => $filters['search'] ?? null,
        'type' => $filters['type'] ?? null,
    ]);
@endphp

<a href="{{ route('admin.media.index', $query) }}"
   class="block rounded-[var(--radius-admin)] px-3 py-2 admin-focus-ring {{ $isActive ? 'bg-admin-accent-muted font-medium admin-text' : 'admin-text-secondary hover:bg-admin-accent-muted/60' }}"
   style="padding-left: {{ 0.75 + ($depth * 0.75) }}rem">
    {{ $folder->name }}
    <span class="admin-muted">({{ $folder->media_count }})</span>
</a>

@foreach ($folder->children ?? [] as $child)
    @include('admin.media.partials.folder-link', ['folder' => $child, 'depth' => $depth + 1])
@endforeach
