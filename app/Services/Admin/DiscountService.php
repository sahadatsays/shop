<?php

namespace App\Services\Admin;

use App\Models\Discount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DiscountService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Discount::query();

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if (($filters['active'] ?? null) === '1') {
            $query->active();
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    public function find(int $id): Discount
    {
        return Discount::query()->withCount('offers')->findOrFail($id);
    }

    public function create(array $data): Discount
    {
        return Discount::query()->create($this->prepareAttributes($data));
    }

    public function update(Discount $discount, array $data): Discount
    {
        $discount->update($this->prepareAttributes($data));

        return $this->find($discount->id);
    }

    public function delete(Discount $discount): void
    {
        $discount->delete();
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data): array
    {
        return [
            'code' => strtoupper(trim($data['code'])),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'value' => (int) $data['value'],
            'min_order_cents' => filled($data['min_order'] ?? null)
                ? (int) round(((float) $data['min_order']) * 100)
                : null,
            'max_uses' => filled($data['max_uses'] ?? null) ? (int) $data['max_uses'] : null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];
    }
}
