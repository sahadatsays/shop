<?php

namespace App\DTOs\Admin\Dashboard;

readonly class RecentOrderData
{
    public function __construct(
        public string $orderNumber,
        public string $customerName,
        public string $totalFormatted,
        public string $status,
        public string $statusVariant,
        public string $placedAt,
    ) {}
}
