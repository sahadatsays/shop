<?php

namespace App\DTOs\Admin\Dashboard;

readonly class CustomerSummaryData
{
    public function __construct(
        public int $customerId,
        public string $name,
        public string $email,
        public string $joinedAt,
        public int $orderCount,
    ) {}
}
