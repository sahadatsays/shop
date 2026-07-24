<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Support\MoneyFormatter;
use Illuminate\Support\Collection;

class CustomerRewardsService
{
    /**
     * @param  Collection<int, Order>  $orders
     * @return array{
     *     points: int,
     *     points_label: string,
     *     last_order_points: int,
     *     last_order_points_label: string,
     *     redeemable_value: string,
     *     current_tier: string,
     *     next_tier: ?string,
     *     next_tier_threshold: ?int,
     *     points_to_next_tier: ?int,
     *     progress_percent: int,
     * }
     */
    public function summary(Collection $orders): array
    {
        $deliveredOrders = $orders->where('status', OrderStatus::Delivered);
        $orderPoints = (int) floor($deliveredOrders->sum('total_cents') / 100) * (int) config('rewards.points_per_dollar', 1);
        $points = $orderPoints + (int) config('rewards.registration_bonus', 0);

        $lastDelivered = $deliveredOrders->sortByDesc('placed_at')->first();
        $lastOrderPoints = $lastDelivered
            ? (int) floor($lastDelivered->total_cents / 100) * (int) config('rewards.points_per_dollar', 1)
            : 0;

        $tiers = collect(config('rewards.tiers', []))->sortBy('threshold')->values();
        $currentTier = $tiers->last(fn (array $tier): bool => $points >= $tier['threshold']) ?? ['name' => 'Member', 'threshold' => 0];
        $nextTier = $tiers->first(fn (array $tier): bool => $points < $tier['threshold']);

        $nextThreshold = $nextTier['threshold'] ?? null;
        $pointsToNext = $nextThreshold !== null ? max($nextThreshold - $points, 0) : null;

        $progressPercent = 100;

        if ($nextThreshold !== null) {
            $previousThreshold = $currentTier['threshold'];
            $range = max($nextThreshold - $previousThreshold, 1);
            $progressPercent = (int) round((($points - $previousThreshold) / $range) * 100);
            $progressPercent = min(max($progressPercent, 0), 100);
        }

        $redemptionPoints = (int) config('rewards.redemption.points', 100);
        $redemptionValueCents = (int) config('rewards.redemption.value_cents', 500);
        $redeemableValueCents = $redemptionPoints > 0
            ? (int) floor($points / $redemptionPoints) * $redemptionValueCents
            : 0;

        return [
            'points' => $points,
            'points_label' => number_format($points),
            'last_order_points' => $lastOrderPoints,
            'last_order_points_label' => $lastOrderPoints > 0 ? '+'.number_format($lastOrderPoints).' last order' : 'Earn on delivery',
            'redeemable_value' => MoneyFormatter::format($redeemableValueCents),
            'current_tier' => $currentTier['name'],
            'next_tier' => $nextTier['name'] ?? null,
            'next_tier_threshold' => $nextThreshold,
            'points_to_next_tier' => $pointsToNext,
            'progress_percent' => $progressPercent,
        ];
    }
}
