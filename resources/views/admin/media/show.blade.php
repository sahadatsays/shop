<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        <x-admin.form-card title="Preview">
            @if ($media->isImage())
                <div x-data="mediaCropper()" x-on:mouseup.window="endDrag()" x-on:mousemove.window="drag($event); resize($event)">
                    <div x-ref="stage" class="relative overflow-hidden rounded-[var(--radius-admin-lg)] bg-admin-bg">
                        <img x-ref="image" src="{{ $media->url() }}" alt="{{ $media->alt_text ?? $media->title }}" class="block max-h-[32rem] w-full object-contain" @load="onImageLoad">
                        <div
                            class="absolute border-2 border-white shadow-[0_0_0_9999px_rgba(15,23,42,0.45)]"
                            :style="`left:${crop.x}px;top:${crop.y}px;width:${crop.width}px;height:${crop.height}px`"
                            @mousedown="startDrag"
                        >
                            <button type="button" class="absolute -bottom-2 -right-2 size-4 rounded-full bg-white shadow" @mousedown.stop="startResize"></button>
                        </div>
                    </div>

                    @if (auth('admin')->user()?->hasPermission('media.manage'))
                        <form method="POST" action="{{ route('admin.media.crop', $media) }}" class="mt-4 flex flex-wrap gap-3">
                            @csrf
                            <input type="hidden" name="x" :value="crop.x">
                            <input type="hidden" name="y" :value="crop.y">
                            <input type="hidden" name="width" :value="crop.width">
                            <input type="hidden" name="height" :value="crop.height">
                            <input type="hidden" name="scale" :value="scale">
                            <x-admin.button type="submit" variant="secondary" size="sm">Apply crop</x-admin.button>
                        </form>
                    @endif
                </div>
            @else
                <div class="flex min-h-48 items-center justify-center rounded-[var(--radius-admin-lg)] bg-admin-bg admin-muted">
                    <div class="text-center">
                        <p class="font-medium admin-text">{{ strtoupper(pathinfo($media->filename, PATHINFO_EXTENSION)) }} file</p>
                        <a href="{{ $media->url() }}" target="_blank" class="mt-2 inline-block text-sm text-admin-brand">Download / open</a>
                    </div>
                </div>
            @endif
        </x-admin.form-card>

        <div class="space-y-6">
            <x-admin.form-card title="Details">
                <dl class="space-y-3 text-sm">
                    <div><dt class="admin-muted">Filename</dt><dd class="font-medium admin-text">{{ $media->original_filename }}</dd></div>
                    <div><dt class="admin-muted">Size</dt><dd class="admin-text">{{ $media->formattedSize() }}</dd></div>
                    @if ($media->width)
                        <div><dt class="admin-muted">Dimensions</dt><dd class="admin-text">{{ $media->width }} × {{ $media->height }}</dd></div>
                    @endif
                    <div><dt class="admin-muted">Type</dt><dd class="admin-text">{{ $media->mime_type }}</dd></div>
                    <div><dt class="admin-muted">Used in</dt><dd class="admin-text">{{ $media->attachments_count }} place(s)</dd></div>
                </dl>

                <div class="mt-4">
                    <button
                        type="button"
                        @click="window.adminCopyText(@js($media->url())).then(() => window.adminToast?.push({ title: 'URL copied', type: 'success' }))"
                        class="text-sm font-medium text-admin-brand admin-focus-ring"
                    >
                        Copy public URL
                    </button>
                </div>
            </x-admin.form-card>

            @if (auth('admin')->user()?->hasPermission('media.manage'))
                <x-admin.form-card title="Edit metadata">
                    <form method="POST" action="{{ route('admin.media.update', $media) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <x-admin.select label="Folder" name="folder_id">
                            <option value="">Uncategorized</option>
                            @foreach ($flatFolders as $folder)
                                <option value="{{ $folder->id }}" @selected(old('folder_id', $media->folder_id) == $folder->id)>{{ $folder->name }}</option>
                            @endforeach
                        </x-admin.select>
                        <x-admin.input label="Title" name="title" :value="old('title', $media->title)" />
                        <x-admin.input label="Alt text" name="alt_text" :value="old('alt_text', $media->alt_text)" />
                        <x-admin.button type="submit" variant="primary" size="sm">Save details</x-admin.button>
                    </form>
                </x-admin.form-card>

                <x-admin.form-card title="Actions">
                    <div class="flex flex-wrap gap-3">
                        @if ($media->isImage())
                            <form method="POST" action="{{ route('admin.media.optimize', $media) }}">
                                @csrf
                                <x-admin.button type="submit" variant="secondary" size="sm">Optimize image</x-admin.button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.media.destroy', $media) }}" onsubmit="return confirm('Delete this file?');">
                            @csrf
                            @method('DELETE')
                            @if ($media->attachments_count > 0)
                                <input type="hidden" name="force" value="1">
                            @endif
                            <x-admin.button type="submit" variant="danger" size="sm">Delete</x-admin.button>
                        </form>
                    </div>
                    @if ($media->attachments_count > 0)
                        <p class="mt-3 text-xs admin-muted">This file is referenced elsewhere. Deleting will remove all attachments.</p>
                    @endif
                </x-admin.form-card>
            @endif

            @if ($media->attachments->isNotEmpty())
                <x-admin.form-card title="Reuse locations">
                    <ul class="space-y-2 text-sm admin-text-secondary">
                        @foreach ($media->attachments as $attachment)
                            <li>{{ class_basename($attachment->mediable_type) }} #{{ $attachment->mediable_id }} · {{ $attachment->collection }}</li>
                        @endforeach
                    </ul>
                </x-admin.form-card>
            @endif
        </div>
    </div>
</x-layouts.admin>
