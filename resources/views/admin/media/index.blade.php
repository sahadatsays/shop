@php
    $isImages = ($filters['type'] ?? null) === 'image';
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('error')), type: 'error' });
            });
        </script>
    @endif

    <x-admin.page-header title="Media Library" description="Upload, organize, optimize, and reuse images and files across the store.">
        <x-slot:actions>
            @if (auth('admin')->user()?->hasPermission('media.manage'))
                <x-admin.button :href="route('admin.media.picker')" variant="secondary" size="sm">Open picker</x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-[16rem_minmax(0,1fr)]">
        <aside class="space-y-4">
            <x-admin.form-card title="Folders">
                <nav class="space-y-1 text-sm">
                    <a href="{{ route('admin.media.index', array_filter(['search' => $filters['search'] ?? null, 'type' => $filters['type'] ?? null])) }}"
                       @class(['block rounded-[var(--radius-admin)] px-3 py-2 font-medium admin-focus-ring', 'bg-admin-accent-muted admin-text' => ! $currentFolder, 'admin-text-secondary hover:bg-admin-accent-muted/60' => $currentFolder])>
                        All files
                    </a>
                    @foreach ($folders as $folder)
                        @include('admin.media.partials.folder-link', ['folder' => $folder, 'depth' => 0, 'filters' => $filters, 'currentFolder' => $currentFolder])
                    @endforeach
                </nav>

                @if (auth('admin')->user()?->hasPermission('media.folders.manage'))
                    <form method="POST" action="{{ route('admin.media.folders.store') }}" class="mt-4 space-y-2 border-t admin-border pt-4">
                        @csrf
                        @if ($currentFolder)
                            <input type="hidden" name="parent_id" value="{{ $currentFolder }}">
                        @endif
                        <x-admin.input label="New folder" name="name" placeholder="Campaign assets" required />
                        <x-admin.button type="submit" variant="secondary" size="sm" class="w-full">Create folder</x-admin.button>
                    </form>
                @endif
            </x-admin.form-card>
        </aside>

        <div
            x-data="mediaLibrary({ uploadUrl: @js(route('admin.media.store')), folderId: @js($currentFolder) })"
            class="space-y-6"
        >
            @if (auth('admin')->user()?->hasPermission('media.manage'))
                <div
                    @dragover.prevent="handleDragOver"
                    @dragleave.prevent="handleDragLeave"
                    @drop.prevent="handleDrop"
                    :class="dragging ? 'border-admin-brand bg-admin-accent-muted/40' : 'admin-border bg-admin-bg/30'"
                    class="rounded-[var(--radius-admin-lg)] border-2 border-dashed p-8 text-center transition-colors"
                >
                    <p class="text-sm font-medium admin-text">Drag and drop files here</p>
                    <p class="mt-1 text-xs admin-muted">JPG, PNG, WebP, GIF, SVG, PDF up to 10MB</p>
                    <div class="mt-4">
                        <input
                            type="file"
                            x-ref="fileInput"
                            class="sr-only"
                            multiple
                            accept="image/*,.pdf,.svg"
                            @change="handleFileSelect"
                        >
                        <x-admin.button
                            type="button"
                            variant="primary"
                            size="sm"
                            ::disabled="uploading"
                            @click="$refs.fileInput.click()"
                        >
                            <span x-text="uploading ? 'Uploading…' : 'Browse files'"></span>
                        </x-admin.button>
                    </div>
                </div>
            @endif

            <x-admin.data-table>
                <x-slot:toolbar>
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <x-admin.filter-tabs :tabs="[
                            ['label' => 'All files', 'href' => route('admin.media.index', array_filter(['folder_id' => $currentFolder, 'search' => $filters['search'] ?? null])), 'active' => ! $isImages],
                            ['label' => 'Images', 'href' => route('admin.media.index', array_filter(['folder_id' => $currentFolder, 'search' => $filters['search'] ?? null, 'type' => 'image'])), 'active' => $isImages],
                        ]" />

                        <form method="GET" action="{{ route('admin.media.index') }}" class="flex w-full gap-2 lg:w-auto">
                            @if ($currentFolder)
                                <input type="hidden" name="folder_id" value="{{ $currentFolder }}">
                            @endif
                            @if ($isImages)
                                <input type="hidden" name="type" value="image">
                            @endif
                            <div class="relative min-w-0 flex-1 lg:w-72">
                                <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search files…"
                                       class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring">
                            </div>
                            <x-admin.button type="submit" variant="secondary" size="sm">Search</x-admin.button>
                        </form>
                    </div>
                </x-slot:toolbar>

                @if ($mediaItems->isEmpty())
                    <x-admin.empty-state title="No files yet" description="Upload assets to this folder or adjust your search." />
                @else
                    <div class="grid grid-cols-2 gap-4 p-4 sm:grid-cols-3 xl:grid-cols-4 sm:p-6">
                        @foreach ($mediaItems as $item)
                            <article class="group overflow-hidden rounded-[var(--radius-admin-lg)] border admin-border bg-admin-surface shadow-sm">
                                <a href="{{ route('admin.media.show', $item) }}" class="block">
                                    <div class="relative aspect-square bg-admin-bg">
                                        @if ($item->isImage())
                                            <img src="{{ $item->thumbnailUrl() }}" alt="{{ $item->alt_text ?? $item->title }}" class="size-full object-cover">
                                        @else
                                            <div class="flex size-full items-center justify-center admin-muted">
                                                <svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2Z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                <div class="space-y-2 p-3">
                                    <p class="truncate text-sm font-medium admin-text">{{ $item->title ?? $item->original_filename }}</p>
                                    <div class="flex items-center justify-between gap-2 text-xs admin-muted">
                                        <span>{{ $item->formattedSize() }}</span>
                                        @if ($item->attachments_count > 0)
                                            <x-admin.badge variant="brand">{{ $item->attachments_count }} uses</x-admin.badge>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" @click="copyUrl(@js($item->url()))" class="text-xs font-medium text-admin-brand admin-focus-ring">Copy URL</button>
                                        <a href="{{ route('admin.media.show', $item) }}" class="text-xs font-medium admin-muted hover:admin-text">Details</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="border-t admin-border px-4 py-3 sm:px-6">
                        <x-admin.pagination :paginator="$mediaItems" />
                    </div>
                @endif
            </x-admin.data-table>
        </div>
    </div>
</x-layouts.admin>
