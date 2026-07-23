<?php

namespace App\Services\Admin;

use App\Models\Collection as ProductCollection;
use App\Models\Product;
use App\Support\SlugGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CollectionService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = ProductCollection::query()->withCount('products');

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (($filters['featured'] ?? null) === '1') {
            $query->featured();
        }

        return $query->orderBy('sort_order')->latest()->paginate(15)->withQueryString();
    }

    public function find(int $id): ProductCollection
    {
        return ProductCollection::query()
            ->with(['products.category'])
            ->withCount('products')
            ->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $image = null, ?UploadedFile $banner = null): ProductCollection
    {
        return DB::transaction(function () use ($data, $image, $banner): ProductCollection {
            $attributes = $this->prepareAttributes($data);

            if ($image) {
                $attributes['image_path'] = $this->storeImage($image, 'collections');
            }

            if ($banner) {
                $attributes['banner_path'] = $this->storeImage($banner, 'collections/banners');
            }

            $collection = ProductCollection::query()->create($attributes);
            $this->syncProducts($collection, $data['product_ids'] ?? []);

            return $this->find($collection->id);
        });
    }

    public function update(ProductCollection $collection, array $data, ?UploadedFile $image = null, ?UploadedFile $banner = null): ProductCollection
    {
        return DB::transaction(function () use ($collection, $data, $image, $banner): ProductCollection {
            $attributes = $this->prepareAttributes($data, $collection);

            if ($image) {
                $this->deleteFile($collection->image_path);
                $attributes['image_path'] = $this->storeImage($image, 'collections');
            }

            if ($banner) {
                $this->deleteFile($collection->banner_path);
                $attributes['banner_path'] = $this->storeImage($banner, 'collections/banners');
            }

            $collection->update($attributes);
            $this->syncProducts($collection, $data['product_ids'] ?? []);

            return $this->find($collection->id);
        });
    }

    public function delete(ProductCollection $collection): void
    {
        $this->deleteFile($collection->image_path);
        $this->deleteFile($collection->banner_path);
        $collection->delete();
    }

    /**
     * @return Collection<int, ProductCollection>
     */
    public function options(): Collection
    {
        return ProductCollection::query()->orderBy('name')->get(['id', 'name']);
    }

    /**
     * @return Collection<int, Product>
     */
    public function productOptions(): Collection
    {
        return Product::query()->published()->orderBy('name')->get(['id', 'name', 'sku']);
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data, ?ProductCollection $collection = null): array
    {
        $slug = $data['slug'] ?? SlugGenerator::from($data['name']);
        $slug = SlugGenerator::unique($slug, fn (string $candidate): bool => ProductCollection::query()
            ->when($collection, fn ($query) => $query->whereKeyNot($collection->id))
            ->where('slug', $candidate)
            ->exists());

        return [
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ];
    }

    /**
     * @param  array<int, int|string>  $productIds
     */
    private function syncProducts(ProductCollection $collection, array $productIds): void
    {
        $sync = collect($productIds)
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->mapWithKeys(fn (int $id, int $index): array => [$id => ['sort_order' => $index]])
            ->all();

        $collection->products()->sync($sync);
    }

    private function storeImage(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
