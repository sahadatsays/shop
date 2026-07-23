<?php

namespace App\Services\Admin;

use App\Enums\PromoBannerLayout;
use App\Models\PromoBanner;
use App\Support\HomepageSettings;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PromoBannerService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = PromoBanner::query();

        if ($search = $filters['search'] ?? null) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->ordered()->paginate(15)->withQueryString();
    }

    public function find(int $id): PromoBanner
    {
        return PromoBanner::query()->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $image = null): PromoBanner
    {
        $attributes = $this->prepareAttributes($data);

        if ($image) {
            $attributes['image_path'] = $this->storeImage($image);
        }

        $banner = PromoBanner::query()->create($attributes);
        HomepageSettings::clearCache();

        return $banner;
    }

    public function update(PromoBanner $banner, array $data, ?UploadedFile $image = null): PromoBanner
    {
        $attributes = $this->prepareAttributes($data);

        if ($image) {
            $this->deleteFile($banner->image_path);
            $attributes['image_path'] = $this->storeImage($image);
        }

        $banner->update($attributes);
        HomepageSettings::clearCache();

        return $banner->refresh();
    }

    public function delete(PromoBanner $banner): void
    {
        $this->deleteFile($banner->image_path);
        $banner->delete();
        HomepageSettings::clearCache();
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data): array
    {
        return [
            'layout' => PromoBannerLayout::from($data['layout'])->value,
            'title' => $data['title'],
            'button_label' => $data['button_label'] ?? null,
            'url' => $data['url'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];
    }

    private function storeImage(UploadedFile $file): string
    {
        return $file->store('homepage/promo', 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
