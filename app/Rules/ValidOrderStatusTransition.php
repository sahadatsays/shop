<?php

namespace App\Rules;

use App\Enums\OrderStatus;
use App\Models\Order;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidOrderStatusTransition implements ValidationRule
{
    public function __construct(private Order $order) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $target = OrderStatus::tryFrom((string) $value);

        if (! $target) {
            return;
        }

        if (! $this->order->status->canTransitionTo($target)) {
            $fail('This order cannot move from '.$this->order->status->label().' to '.$target->label().'.');
        }
    }
}
