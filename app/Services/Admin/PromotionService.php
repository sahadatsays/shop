<?php

namespace App\Services\Admin;

use App\Enums\PromotionType;
use App\Models\Promotion;
use App\Support\SlugGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PromotionService
{
    public function list(PromotionType $type, array $filters = []): LengthAwarePaginator
    {
        $query = Promotion::query()->with(['collection', 'offer'])->where('type', $type);

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('headline', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('sort_order')->latest()->paginate(15)->withQueryString();
    }

    public function find(int $id): Promotion
    {
        return Promotion::query()->with(['collection', 'offer'])->findOrFail($id);
    }

    public function create(PromotionType $type, array $data, ?UploadedFile $image = null): Promotion
    {
        $attributes = $this->prepareAttributes($type, $data);

        if ($image) {
            $attributes['image_path'] = $this->storeImage($image);
        }

        return Promotion::query()->create($attributes);
    }

    public function update(Promotion $promotion, array $data, ?UploadedFile $image = null): Promotion
    {
        $attributes = $this->prepareAttributes($promotion->type, $data, $promotion);

        if ($image) {
            $this->deleteFile($promotion->image_path);
            $attributes['image_path'] = $this->storeImage($image);
        }

        $promotion->update($attributes);

        return $this->find($promotion->id);
    }

    public function delete(Promotion $promotion): void
    {
        $this->deleteFile($promotion->image_path);
        $promotion->delete();
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(PromotionType $type, array $data, ?Promotion $promotion = null): array
    {
        $slug = $data['slug'] ?? SlugGenerator::from($data['name']);
        $slug = SlugGenerator::unique($slug, fn (string $candidate): bool => Promotion::query()
            ->when($promotion, fn ($query) => $query->whereKeyNot($promotion->id))
            ->where('slug', $candidate)
            ->exists());

        return [
            'name' => $data['name'],
            'slug' => $slug,
            'type' => $type->value,
            'placement' => $data['placement'],
            'headline' => $data['headline'],
            'subheadline' => $data['subheadline'] ?? null,
            'body' => $data['body'] ?? null,
            'cta_label' => $data['cta_label'] ?? null,
            'cta_url' => $data['cta_url'] ?? null,
            'collection_id' => $data['collection_id'] ?? null,
            'offer_id' => $data['offer_id'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function storeImage(UploadedFile $file): string
    {
        return $file->store('promotions', 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
