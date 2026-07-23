<?php

namespace App\Services\Admin;

use App\Models\HeroBanner;
use App\Support\HomepageSettings;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HeroBannerService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = HeroBanner::query();

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        if (($filters['status'] ?? null) === 'active') {
            $query->where('is_active', true);
        }

        if (($filters['status'] ?? null) === 'inactive') {
            $query->where('is_active', false);
        }

        return $query->ordered()->paginate(15)->withQueryString();
    }

    public function find(int $id): HeroBanner
    {
        return HeroBanner::query()->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $desktopImage = null, ?UploadedFile $mobileImage = null): HeroBanner
    {
        $attributes = $this->prepareAttributes($data);

        if ($desktopImage) {
            $attributes['desktop_image_path'] = $this->storeImage($desktopImage, 'homepage/hero/desktop');
        }

        if ($mobileImage) {
            $attributes['mobile_image_path'] = $this->storeImage($mobileImage, 'homepage/hero/mobile');
        }

        $banner = HeroBanner::query()->create($attributes);
        HomepageSettings::clearCache();

        return $banner;
    }

    public function update(HeroBanner $banner, array $data, ?UploadedFile $desktopImage = null, ?UploadedFile $mobileImage = null): HeroBanner
    {
        $attributes = $this->prepareAttributes($data);

        if ($desktopImage) {
            $this->deleteFile($banner->desktop_image_path);
            $attributes['desktop_image_path'] = $this->storeImage($desktopImage, 'homepage/hero/desktop');
        }

        if ($mobileImage) {
            $this->deleteFile($banner->mobile_image_path);
            $attributes['mobile_image_path'] = $this->storeImage($mobileImage, 'homepage/hero/mobile');
        }

        $banner->update($attributes);
        HomepageSettings::clearCache();

        return $banner->refresh();
    }

    public function delete(HeroBanner $banner): void
    {
        $this->deleteFile($banner->desktop_image_path);
        $this->deleteFile($banner->mobile_image_path);
        $banner->delete();
        HomepageSettings::clearCache();
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data): array
    {
        return [
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,
            'badge_text' => $data['badge_text'] ?? null,
            'primary_label' => $data['primary_label'] ?? null,
            'primary_url' => $data['primary_url'] ?? null,
            'secondary_label' => $data['secondary_label'] ?? null,
            'secondary_url' => $data['secondary_url'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];
    }

    private function storeImage(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
