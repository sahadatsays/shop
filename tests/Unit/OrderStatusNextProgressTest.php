<?php

use App\Enums\OrderStatus;

test('next progress status follows the fulfillment flow one step at a time', function (OrderStatus $current, ?OrderStatus $expected): void {
    expect($current->nextProgressStatus())->toBe($expected)
        ->and($current->canAdvanceProgress())->toBe($expected !== null);
})->with([
    'pending' => [OrderStatus::Pending, OrderStatus::Confirmed],
    'confirmed' => [OrderStatus::Confirmed, OrderStatus::Processing],
    'processing' => [OrderStatus::Processing, OrderStatus::Packed],
    'packed' => [OrderStatus::Packed, OrderStatus::Shipped],
    'shipped' => [OrderStatus::Shipped, OrderStatus::Delivered],
    'delivered' => [OrderStatus::Delivered, null],
    'cancelled' => [OrderStatus::Cancelled, null],
    'returned' => [OrderStatus::Returned, null],
    'refunded' => [OrderStatus::Refunded, null],
]);
