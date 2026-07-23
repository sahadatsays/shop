<?php

namespace App\Services\Admin;

use App\Models\Media;
use App\Models\Mediable;
use App\Models\MediaFolder;
use App\Support\Media\MediaImageProcessor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function __construct(private MediaImageProcessor $images) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Media::query()
            ->with(['folder', 'uploader'])
            ->withCount('attachments')
            ->latest();

        if ($folderId = $filters['folder_id'] ?? null) {
            $query->where('folder_id', $folderId);
        }

        if (($filters['type'] ?? null) === 'image') {
            $query->where('mime_type', 'like', 'image/%');
        }

        if ($search = $filters['search'] ?? null) {
            $term = '%'.$search.'%';
            $query->where(function ($builder) use ($term): void {
                $builder->where('filename', 'like', $term)
                    ->orWhere('original_filename', 'like', $term)
                    ->orWhere('title', 'like', $term)
                    ->orWhere('alt_text', 'like', $term);
            });
        }

        return $query->paginate(24)->withQueryString();
    }

    public function find(int $id): Media
    {
        return Media::query()
            ->with(['folder', 'uploader', 'attachments.mediable'])
            ->withCount('attachments')
            ->findOrFail($id);
    }

    public function upload(UploadedFile $file, ?int $folderId = null, ?int $uploadedBy = null): Media
    {
        $folder = $folderId ? MediaFolder::query()->findOrFail($folderId) : null;
        $hash = hash_file('sha256', $file->getRealPath() ?: '');

        $existing = Media::query()
            ->when($folderId, fn ($query) => $query->where('folder_id', $folderId))
            ->where('hash', $hash)
            ->first();

        if ($existing) {
            return $existing;
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $filename = Str::uuid()->toString().'.'.$extension;
        $directory = $folder ? $folder->storagePath() : 'media/uncategorized';
        $path = $file->storeAs($directory, $filename, 'public');
        $absolutePath = Storage::disk('public')->path($path);

        $width = null;
        $height = null;
        $thumbnailPath = null;

        if (str_starts_with($file->getMimeType() ?: '', 'image/')) {
            $dimensions = $this->images->optimize($absolutePath);
            $width = $dimensions['width'];
            $height = $dimensions['height'];

            $thumbnailPath = $directory.'/thumbs/'.$filename;
            $this->images->createThumbnail($absolutePath, Storage::disk('public')->path($thumbnailPath));
        }

        return Media::query()->create([
            'folder_id' => $folderId,
            'disk' => 'public',
            'path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => (int) Storage::disk('public')->size($path),
            'width' => $width,
            'height' => $height,
            'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'hash' => $hash,
            'uploaded_by' => $uploadedBy,
        ]);
    }

    public function update(Media $media, array $data): Media
    {
        $media->update([
            'folder_id' => $data['folder_id'] ?? $media->folder_id,
            'title' => $data['title'] ?? $media->title,
            'alt_text' => $data['alt_text'] ?? $media->alt_text,
        ]);

        return $media->fresh(['folder', 'uploader']);
    }

    /**
     * @param  array{x: float, y: float, width: float, height: float, scale: float}  $crop
     */
    public function crop(Media $media, array $crop): Media
    {
        if (! $media->isImage()) {
            throw new \InvalidArgumentException('Only images can be cropped.');
        }

        $scale = max($crop['scale'], 0.01);
        $absolutePath = Storage::disk($media->disk)->path($media->path);

        $dimensions = $this->images->crop(
            $absolutePath,
            (int) round($crop['x'] / $scale),
            (int) round($crop['y'] / $scale),
            (int) round($crop['width'] / $scale),
            (int) round($crop['height'] / $scale),
        );

        if ($media->thumbnail_path) {
            $this->images->createThumbnail($absolutePath, Storage::disk($media->disk)->path($media->thumbnail_path));
        }

        $media->update([
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'size' => (int) Storage::disk($media->disk)->size($media->path),
            'hash' => hash_file('sha256', $absolutePath),
            'meta' => array_merge($media->meta ?? [], [
                'cropped_at' => now()->toIso8601String(),
            ]),
        ]);

        return $media->fresh();
    }

    public function optimize(Media $media): Media
    {
        if (! $media->isImage()) {
            throw new \InvalidArgumentException('Only images can be optimized.');
        }

        $absolutePath = Storage::disk($media->disk)->path($media->path);
        $dimensions = $this->images->optimize($absolutePath);

        if ($media->thumbnail_path) {
            $this->images->createThumbnail($absolutePath, Storage::disk($media->disk)->path($media->thumbnail_path));
        }

        $media->update([
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'size' => (int) Storage::disk($media->disk)->size($media->path),
            'hash' => hash_file('sha256', $absolutePath),
            'meta' => array_merge($media->meta ?? [], [
                'optimized_at' => now()->toIso8601String(),
            ]),
        ]);

        return $media->fresh();
    }

    public function delete(Media $media, bool $force = false): void
    {
        if (! $force && $media->usageCount() > 0) {
            throw new \InvalidArgumentException('This file is in use and cannot be deleted.');
        }

        Storage::disk($media->disk)->delete(array_filter([
            $media->path,
            $media->thumbnail_path,
        ]));

        $media->attachments()->delete();
        $media->delete();
    }

    public function attach(Media $media, Model $model, string $collection = 'default', int $sortOrder = 0): Mediable
    {
        return Mediable::query()->updateOrCreate(
            [
                'media_id' => $media->id,
                'mediable_type' => $model->getMorphClass(),
                'mediable_id' => $model->getKey(),
                'collection' => $collection,
            ],
            ['sort_order' => $sortOrder],
        );
    }
}
