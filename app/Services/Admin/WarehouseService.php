<?php

namespace App\Services\Admin;

use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WarehouseService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Warehouse::query()
            ->withSum('stockLevels as total_stock', 'quantity')
            ->withCount(['stockLevels', 'movements']);

        if ($search = $filters['search'] ?? null) {
            $term = '%'.$search.'%';
            $query->where(function ($builder) use ($term): void {
                $builder->where('name', 'like', $term)
                    ->orWhere('code', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('state', 'like', $term);
            });
        }

        if (($isActive = $filters['is_active'] ?? null) !== null && $isActive !== '') {
            $query->where('is_active', filter_var($isActive, FILTER_VALIDATE_BOOLEAN));
        }

        return $query->ordered()->paginate(15)->withQueryString();
    }

    public function find(int $id): Warehouse
    {
        return Warehouse::query()
            ->withSum('stockLevels as total_stock', 'quantity')
            ->withCount(['stockLevels', 'movements'])
            ->findOrFail($id);
    }

    public function create(array $data): Warehouse
    {
        return DB::transaction(function () use ($data): Warehouse {
            $warehouse = Warehouse::query()->create($this->prepareAttributes($data));

            if ($warehouse->is_default) {
                $this->clearOtherDefaults($warehouse);
            } elseif (! Warehouse::query()->where('is_default', true)->exists()) {
                $warehouse->update(['is_default' => true]);
            }

            return $this->find($warehouse->id);
        });
    }

    public function update(Warehouse $warehouse, array $data): Warehouse
    {
        return DB::transaction(function () use ($warehouse, $data): Warehouse {
            $attributes = $this->prepareAttributes($data, $warehouse);
            $willBeDefault = (bool) ($attributes['is_default'] ?? false);

            if (! $willBeDefault && $warehouse->is_default) {
                $otherDefaults = Warehouse::query()
                    ->whereKeyNot($warehouse->id)
                    ->where('is_default', true)
                    ->exists();

                if (! $otherDefaults) {
                    throw ValidationException::withMessages([
                        'is_default' => 'Assign another default warehouse before removing this default.',
                    ]);
                }
            }

            $warehouse->update($attributes);

            if ($warehouse->is_default) {
                $this->clearOtherDefaults($warehouse);
            }

            return $this->find($warehouse->id);
        });
    }

    public function delete(Warehouse $warehouse): void
    {
        if ($warehouse->is_default && Warehouse::query()->count() > 1) {
            throw ValidationException::withMessages([
                'warehouse' => 'Set another warehouse as default before deleting this one.',
            ]);
        }

        $hasStock = WarehouseStock::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('quantity', '>', 0)
            ->exists();

        if ($hasStock) {
            throw ValidationException::withMessages([
                'warehouse' => 'Transfer or clear warehouse stock before deleting this location.',
            ]);
        }

        if ($warehouse->movements()->exists()) {
            throw ValidationException::withMessages([
                'warehouse' => 'Warehouses with stock history cannot be deleted.',
            ]);
        }

        $warehouse->delete();

        if (! Warehouse::query()->where('is_default', true)->exists()) {
            Warehouse::query()->ordered()->first()?->update(['is_default' => true]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data, ?Warehouse $warehouse = null): array
    {
        return [
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? config('store.country_code', 'BD'),
            'address' => $data['address'] ?? null,
            'is_default' => (bool) ($data['is_default'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function clearOtherDefaults(Warehouse $warehouse): void
    {
        Warehouse::query()
            ->whereKeyNot($warehouse->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }
}
