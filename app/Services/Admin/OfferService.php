<?php

namespace App\Services\Admin;

use App\Models\Offer;
use App\Models\Product;
use App\Support\SlugGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OfferService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Offer::query()->with('discount')->withCount('products');

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('headline', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('sort_order')->latest()->paginate(15)->withQueryString();
    }

    public function find(int $id): Offer
    {
        return Offer::query()
            ->with(['discount', 'products.category'])
            ->withCount('products')
            ->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $image = null): Offer
    {
        return DB::transaction(function () use ($data, $image): Offer {
            $attributes = $this->prepareAttributes($data);

            if ($image) {
                $attributes['image_path'] = $this->storeImage($image);
            }

            $offer = Offer::query()->create($attributes);
            $this->syncProducts($offer, $data['products'] ?? []);

            return $this->find($offer->id);
        });
    }

    public function update(Offer $offer, array $data, ?UploadedFile $image = null): Offer
    {
        return DB::transaction(function () use ($offer, $data, $image): Offer {
            $attributes = $this->prepareAttributes($data, $offer);

            if ($image) {
                $this->deleteFile($offer->image_path);
                $attributes['image_path'] = $this->storeImage($image);
            }

            $offer->update($attributes);
            $this->syncProducts($offer, $data['products'] ?? []);

            return $this->find($offer->id);
        });
    }

    public function delete(Offer $offer): void
    {
        $this->deleteFile($offer->image_path);
        $offer->delete();
    }

    /**
     * @return Collection<int, Product>
     */
    public function productOptions(): Collection
    {
        return Product::query()->published()->orderBy('name')->get(['id', 'name', 'sku', 'price_cents', 'compare_at_price_cents']);
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data, ?Offer $offer = null): array
    {
        $slug = $data['slug'] ?? SlugGenerator::from($data['name']);
        $slug = SlugGenerator::unique($slug, fn (string $candidate): bool => Offer::query()
            ->when($offer, fn ($query) => $query->whereKeyNot($offer->id))
            ->where('slug', $candidate)
            ->exists());

        return [
            'name' => $data['name'],
            'slug' => $slug,
            'headline' => $data['headline'],
            'subheadline' => $data['subheadline'] ?? null,
            'body' => $data['body'] ?? null,
            'cta_label' => $data['cta_label'] ?? null,
            'cta_url' => $data['cta_url'] ?? null,
            'discount_id' => $data['discount_id'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function syncProducts(Offer $offer, array $rows): void
    {
        $sync = [];

        foreach (collect($rows)->filter(fn (array $row): bool => filled($row['product_id'] ?? null))->values() as $index => $row) {
            $productId = (int) $row['product_id'];
            $salePriceCents = filled($row['sale_price'] ?? null)
                ? (int) round(((float) $row['sale_price']) * 100)
                : null;

            $sync[$productId] = [
                'sale_price_cents' => $salePriceCents,
                'sort_order' => $index,
            ];

            if ($salePriceCents !== null) {
                $product = Product::query()->find($productId);

                if ($product) {
                    $compareAt = max($product->price_cents, $salePriceCents + 1);
                    $product->update([
                        'compare_at_price_cents' => $compareAt,
                        'price_cents' => $salePriceCents,
                    ]);
                }
            }
        }

        $offer->products()->sync($sync);
    }

    private function storeImage(UploadedFile $file): string
    {
        return $file->store('offers', 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
