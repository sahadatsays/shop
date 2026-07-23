<?php

namespace App\Services\Admin;

use App\Contracts\Repositories\AdminProductRepositoryInterface;
use App\DTOs\Admin\Product\ProductFormData;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\SlugGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        private AdminProductRepositoryInterface $products,
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->products->paginate($filters);
    }

    public function formData(?Product $product = null): ProductFormData
    {
        if ($product) {
            $product->load(['images', 'specifications', 'attributes', 'relatedProducts']);
        }

        return new ProductFormData(
            product: $product,
            categoryOptions: Category::query()->active()->ordered()->get(['id', 'name']),
            brandOptions: Brand::query()->active()->ordered()->get(['id', 'name']),
            relatedProductOptions: $this->products->options($product?->id),
        );
    }

    /**
     * @param  array<int, UploadedFile>  $gallery
     */
    public function create(array $data, array $gallery = []): Product
    {
        return DB::transaction(function () use ($data, $gallery): Product {
            $product = $this->products->create($this->prepareAttributes($data));

            $this->syncSpecifications($product, $data['specifications'] ?? []);
            $this->syncAttributes($product, $data['attributes'] ?? []);
            $this->syncRelatedProducts($product, $data['related_product_ids'] ?? []);
            $this->storeGalleryImages($product, $gallery);

            return $this->products->find($product->id);
        });
    }

    /**
     * @param  array<int, UploadedFile>  $gallery
     */
    public function update(Product $product, array $data, array $gallery = []): Product
    {
        return DB::transaction(function () use ($product, $data, $gallery): Product {
            $product = $this->products->update($product, $this->prepareAttributes($data, $product));

            $this->removeImages($product, $data['remove_images'] ?? []);
            $this->syncSpecifications($product, $data['specifications'] ?? []);
            $this->syncAttributes($product, $data['attributes'] ?? []);
            $this->syncRelatedProducts($product, $data['related_product_ids'] ?? []);
            $this->storeGalleryImages($product, $gallery);

            return $this->products->find($product->id);
        });
    }

    public function delete(Product $product): void
    {
        $this->products->delete($product);
    }

    public function restore(int $id): Product
    {
        return $this->products->restore($id);
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data, ?Product $product = null): array
    {
        $slug = $data['slug'] ?? SlugGenerator::from($data['name']);

        $slug = SlugGenerator::unique($slug, fn (string $candidate): bool => $this->products->slugExists(
            $candidate,
            $product?->id,
        ));

        return [
            'category_id' => $data['category_id'],
            'brand_id' => ! empty($data['brand_id']) ? $data['brand_id'] : null,
            'name' => $data['name'],
            'slug' => $slug,
            'sku' => $data['sku'],
            'barcode' => $data['barcode'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'price_cents' => $this->priceToCents($data['price']),
            'stock_quantity' => (int) ($data['stock_quantity'] ?? 0),
            'low_stock_threshold' => (int) ($data['low_stock_threshold'] ?? 10),
            'status' => $data['status'],
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'is_new_arrival' => (bool) ($data['is_new_arrival'] ?? false),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function priceToCents(string|float|int $price): int
    {
        return (int) round(((float) $price) * 100);
    }

    /**
     * @param  array<int, array{name?: string, value?: string}>  $specifications
     */
    private function syncSpecifications(Product $product, array $specifications): void
    {
        $product->specifications()->delete();

        foreach ($this->filterKeyValueRows($specifications) as $index => $row) {
            $product->specifications()->create([
                'name' => $row['name'],
                'value' => $row['value'],
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * @param  array<int, array{name?: string, value?: string}>  $attributes
     */
    private function syncAttributes(Product $product, array $attributes): void
    {
        $product->attributes()->delete();

        foreach ($this->filterKeyValueRows($attributes) as $index => $row) {
            $product->attributes()->create([
                'name' => $row['name'],
                'value' => $row['value'],
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * @param  array<int, array{name?: string, value?: string}>  $rows
     * @return array<int, array{name: string, value: string}>
     */
    private function filterKeyValueRows(array $rows): array
    {
        return collect($rows)
            ->filter(fn (array $row): bool => filled($row['name'] ?? null) && filled($row['value'] ?? null))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int|string>  $relatedProductIds
     */
    private function syncRelatedProducts(Product $product, array $relatedProductIds): void
    {
        $syncData = collect($relatedProductIds)
            ->filter(fn ($id): bool => (int) $id !== $product->id)
            ->unique()
            ->values()
            ->mapWithKeys(fn ($id, $index): array => [(int) $id => ['sort_order' => $index]])
            ->all();

        $product->relatedProducts()->sync($syncData);
    }

    /**
     * @param  array<int, UploadedFile>  $files
     */
    private function storeGalleryImages(Product $product, array $files): void
    {
        if ($files === []) {
            return;
        }

        $startOrder = (int) $product->images()->max('sort_order') + 1;
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($files as $index => $file) {
            $path = $file->store('products/gallery', 'public');

            $product->images()->create([
                'path' => $path,
                'alt_text' => $product->name,
                'sort_order' => $startOrder + $index,
                'is_primary' => ! $hasPrimary && $index === 0,
            ]);
        }
    }

    /**
     * @param  array<int, int|string>  $imageIds
     */
    private function removeImages(Product $product, array $imageIds): void
    {
        if ($imageIds === []) {
            return;
        }

        $images = $product->images()->whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            $this->deleteFile($image->path);
            $image->delete();
        }

        if (! $product->images()->where('is_primary', true)->exists()) {
            $product->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
