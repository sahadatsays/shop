<?php

namespace App\DTOs\Admin\Dashboard;

readonly class ChartData
{
    /**
     * @param  array<int, string>  $labels
     * @param  array<int, array{name: string, data: array<int, float|int>}>  $series
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $type,
        public array $labels,
        public array $series,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'labels' => $this->labels,
            'series' => $this->series,
        ];
    }
}
