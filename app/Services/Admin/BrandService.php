<?php

namespace App\Services\Admin;

use App\Contracts\Repositories\AdminBrandRepositoryInterface;
use App\DTOs\Admin\Brand\BrandFormData;
use App\Models\Brand;
use App\Support\SlugGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BrandService
{
    public function __construct(
        private AdminBrandRepositoryInterface $brands,
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->brands->paginate($filters);
    }

    public function formData(?Brand $brand = null): BrandFormData
    {
        return new BrandFormData(brand: $brand);
    }

    public function create(array $data, ?UploadedFile $logo = null): Brand
    {
        $attributes = $this->prepareAttributes($data);

        if ($logo) {
            $attributes['logo_path'] = $this->storeLogo($logo);
        }

        return $this->brands->create($attributes);
    }

    public function update(Brand $brand, array $data, ?UploadedFile $logo = null): Brand
    {
        $attributes = $this->prepareAttributes($data, $brand);

        if ($logo) {
            $this->deleteFile($brand->logo_path);
            $attributes['logo_path'] = $this->storeLogo($logo);
        }

        return $this->brands->update($brand, $attributes);
    }

    public function delete(Brand $brand): void
    {
        $this->brands->delete($brand);
    }

    public function restore(int $id): Brand
    {
        return $this->brands->restore($id);
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data, ?Brand $brand = null): array
    {
        $slug = $data['slug'] ?? SlugGenerator::from($data['name']);

        $slug = SlugGenerator::unique($slug, fn (string $candidate): bool => $this->brands->slugExists(
            $candidate,
            $brand?->id,
        ));

        return [
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function storeLogo(UploadedFile $file): string
    {
        return $file->store('brands/logos', 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
