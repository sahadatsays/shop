<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CropMediaRequest;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Http\Requests\Admin\UpdateMediaRequest;
use App\Models\Media;
use App\Services\Admin\MediaFolderService;
use App\Services\Admin\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(
        private MediaService $mediaService,
        private MediaFolderService $folders,
    ) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('media.view'), 403);

        return view('admin.media.index', [
            'title' => 'Media Library',
            'breadcrumbs' => [['label' => 'Content'], ['label' => 'Media Library']],
            'mediaItems' => $this->mediaService->list($request->only(['search', 'folder_id', 'type'])),
            'folders' => $this->folders->tree(),
            'flatFolders' => $this->folders->flat(),
            'filters' => $request->only(['search', 'folder_id', 'type']),
            'currentFolder' => $request->integer('folder_id') ?: null,
        ]);
    }

    public function picker(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('media.view'), 403);

        return view('admin.media.picker', [
            'mediaItems' => $this->mediaService->list($request->only(['search', 'folder_id', 'type'])),
            'flatFolders' => $this->folders->flat(),
            'filters' => $request->only(['search', 'folder_id', 'type']),
        ]);
    }

    public function show(Media $media): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('media.view'), 403);

        $media = $this->mediaService->find($media->id);

        return view('admin.media.show', [
            'title' => $media->title ?? $media->original_filename,
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Media Library', 'href' => route('admin.media.index')],
                ['label' => $media->title ?? $media->original_filename],
            ],
            'media' => $media,
            'flatFolders' => $this->folders->flat(),
        ]);
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $uploaded = [];

        try {
            foreach ($request->file('files', []) as $file) {
                $uploaded[] = $this->mediaService->upload(
                    $file,
                    $request->integer('folder_id') ?: null,
                    Auth::guard('admin')->id(),
                );
            }
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Upload failed while processing the file.',
                'errors' => [
                    'files' => [$exception->getMessage()],
                ],
            ], 422);
        }

        return response()->json([
            'message' => count($uploaded).' file(s) uploaded successfully.',
            'media' => collect($uploaded)->map(fn (Media $item): array => [
                'id' => $item->id,
                'url' => $item->url(),
                'thumbnail_url' => $item->thumbnailUrl(),
                'title' => $item->title,
            ]),
        ]);
    }

    public function update(UpdateMediaRequest $request, Media $media): RedirectResponse
    {
        $this->mediaService->update($media, $request->validated());

        return redirect()
            ->route('admin.media.show', $media)
            ->with('success', 'Media details updated.');
    }

    public function crop(CropMediaRequest $request, Media $media): RedirectResponse
    {
        $this->mediaService->crop($media, $request->validated());

        return redirect()
            ->route('admin.media.show', $media)
            ->with('success', 'Image cropped successfully.');
    }

    public function optimize(Media $media): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('media.manage'), 403);

        $this->mediaService->optimize($media);

        return redirect()
            ->route('admin.media.show', $media)
            ->with('success', 'Image optimized successfully.');
    }

    public function destroy(Request $request, Media $media): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('media.manage'), 403);

        $folderId = $media->folder_id;
        $this->mediaService->delete($media, $request->boolean('force'));

        return redirect()
            ->route('admin.media.index', array_filter(['folder_id' => $folderId]))
            ->with('success', 'Media deleted successfully.');
    }
}
