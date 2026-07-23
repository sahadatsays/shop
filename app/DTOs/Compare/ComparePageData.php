<?php

namespace App\DTOs\Compare;

use Illuminate\Support\Collection;

class ComparePageData
{
    /**
     * @param  Collection<int, CompareProductColumn>  $columns
     * @param  list<string>  $specificationLabels
     */
    public function __construct(
        public Collection $columns,
        public array $specificationLabels,
    ) {}

    public function isEmpty(): bool
    {
        return $this->columns->isEmpty();
    }

    public function count(): int
    {
        return $this->columns->count();
    }

    public function hasMaterials(): bool
    {
        return $this->columns->contains(fn (CompareProductColumn $column): bool => $column->materials !== '');
    }

    public function hasCare(): bool
    {
        return $this->columns->contains(fn (CompareProductColumn $column): bool => $column->care !== '');
    }

    public function hasSizes(): bool
    {
        return $this->columns->contains(fn (CompareProductColumn $column): bool => $column->sizes !== '');
    }

    public function hasWarranty(): bool
    {
        return $this->columns->contains(fn (CompareProductColumn $column): bool => $column->warranty !== null);
    }
}
