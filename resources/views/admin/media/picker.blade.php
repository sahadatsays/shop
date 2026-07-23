<x-layouts.admin title="Media Picker">
    <x-admin.page-header title="Media Picker" description="Select an existing file to reuse in your content." />

    <form method="GET" action="{{ route('admin.media.picker') }}" class="mb-6 flex flex-wrap gap-2">
        <x-admin.select name="folder_id" class="min-w-48">
            <option value="">All folders</option>
            @foreach ($flatFolders as $folder)
                <option value="{{ $folder->id }}" @selected(($filters['folder_id'] ?? null) == $folder->id)>{{ $folder->name }}</option>
            @endforeach
        </x-admin.select>
        <x-admin.input name="search" :value="$filters['search'] ?? ''" placeholder="Search files…" />
        <x-admin.button type="submit" variant="secondary" size="sm">Filter</x-admin.button>
    </form>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6">
        @forelse ($mediaItems as $item)
            <button
                type="button"
                class="overflow-hidden rounded-[var(--radius-admin-lg)] border admin-border bg-admin-surface text-left admin-focus-ring hover:border-admin-brand"
                onclick="window.opener?.postMessage({ type: 'media-selected', media: @js(['id' => $item->id, 'url' => $item->url(), 'title' => $item->title]) }, '*'); window.close();"
            >
                <div class="aspect-square bg-admin-bg">
                    @if ($item->isImage())
                        <img src="{{ $item->thumbnailUrl() }}" alt="" class="size-full object-cover">
                    @endif
                </div>
                <p class="truncate p-2 text-xs font-medium admin-text">{{ $item->title ?? $item->original_filename }}</p>
            </button>
        @empty
            <p class="col-span-full text-sm admin-muted">No media found.</p>
        @endforelse
    </div>
</x-layouts.admin>
