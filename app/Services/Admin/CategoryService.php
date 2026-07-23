<?php

namespace App\Services\Admin;

use App\Contracts\Repositories\AdminCategoryRepositoryInterface;
use App\DTOs\Admin\Category\CategoryFormData;
use App\Models\Category;
use App\Support\SlugGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    public function __construct(
        private AdminCategoryRepositoryInterface $categories,
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->categories->paginate($filters);
    }

    public function formData(?Category $category = null): CategoryFormData
    {
        return new CategoryFormData(
            category: $category,
            parentOptions: $this->categories->parentOptions($category?->id),
        );
    }

    public function create(array $data, ?UploadedFile $image = null, ?UploadedFile $banner = null): Category
    {
        $attributes = $this->prepareAttributes($data);

        if ($image) {
            $attributes['image_path'] = $this->storeImage($image, 'images');
        }

        if ($banner) {
            $attributes['banner_path'] = $this->storeImage($banner, 'banners');
        }

        return $this->categories->create($attributes);
    }

    public function update(Category $category, array $data, ?UploadedFile $image = null, ?UploadedFile $banner = null): Category
    {
        $attributes = $this->prepareAttributes($data, $category);

        if ($image) {
            $this->deleteFile($category->image_path);
            $attributes['image_path'] = $this->storeImage($image, 'images');
        }

        if ($banner) {
            $this->deleteFile($category->banner_path);
            $attributes['banner_path'] = $this->storeImage($banner, 'banners');
        }

        return $this->categories->update($category, $attributes);
    }

    public function delete(Category $category): void
    {
        $this->categories->delete($category);
    }

    public function restore(int $id): Category
    {
        return $this->categories->restore($id);
    }

    /**
     * @return Collection<int, Category>
     */
    public function parentOptions(?int $excludeId = null): Collection
    {
        return $this->categories->parentOptions($excludeId);
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data, ?Category $category = null): array
    {
        $slug = $data['slug'] ?? SlugGenerator::from($data['name']);

        $slug = SlugGenerator::unique($slug, fn (string $candidate): bool => $this->categories->slugExists(
            $candidate,
            $category?->id,
        ));

        return [
            'parent_id' => ! empty($data['parent_id']) ? $data['parent_id'] : null,
            'name' => $data['name'],
            'slug' => $slug,
            'status' => $data['status'],
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function storeImage(UploadedFile $file, string $folder): string
    {
        return $file->store("categories/{$folder}", 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
