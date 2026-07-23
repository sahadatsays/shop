<?php

namespace App\Services\Admin;

use App\Models\MediaFolder;
use App\Support\SlugGenerator;
use Illuminate\Support\Collection;

class MediaFolderService
{
    /**
     * @return Collection<int, MediaFolder>
     */
    public function tree(): Collection
    {
        $folders = MediaFolder::query()
            ->withCount('media')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $this->buildTree($folders);
    }

    /**
     * @return Collection<int, MediaFolder>
     */
    public function flat(): Collection
    {
        return MediaFolder::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): MediaFolder
    {
        $parentId = $data['parent_id'] ?? null;
        $slug = SlugGenerator::unique($data['name'], fn (string $candidate): bool => MediaFolder::query()
            ->where('parent_id', $parentId)
            ->where('slug', $candidate)
            ->exists());

        return MediaFolder::query()->create([
            'parent_id' => $parentId,
            'name' => $data['name'],
            'slug' => $slug,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);
    }

    public function update(MediaFolder $folder, array $data): MediaFolder
    {
        $parentId = $data['parent_id'] ?? $folder->parent_id;
        $slug = $data['name'] !== $folder->name
            ? SlugGenerator::unique($data['name'], fn (string $candidate): bool => MediaFolder::query()
                ->where('parent_id', $parentId)
                ->where('slug', $candidate)
                ->whereKeyNot($folder->id)
                ->exists())
            : $folder->slug;

        $folder->update([
            'parent_id' => $parentId,
            'name' => $data['name'],
            'slug' => $slug,
            'sort_order' => (int) ($data['sort_order'] ?? $folder->sort_order),
        ]);

        return $folder->fresh(['children']);
    }

    public function delete(MediaFolder $folder): void
    {
        if ($folder->children()->exists()) {
            throw new \InvalidArgumentException('Remove subfolders before deleting this folder.');
        }

        if ($folder->media()->exists()) {
            throw new \InvalidArgumentException('Move or delete media files before deleting this folder.');
        }

        $folder->delete();
    }

    /**
     * @param  Collection<int, MediaFolder>  $folders
     * @return Collection<int, MediaFolder>
     */
    private function buildTree(Collection $folders, ?int $parentId = null): Collection
    {
        return $folders
            ->where('parent_id', $parentId)
            ->values()
            ->each(function (MediaFolder $folder) use ($folders): void {
                $folder->setRelation('children', $this->buildTree($folders, $folder->id));
            });
    }
}
