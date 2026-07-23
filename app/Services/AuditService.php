<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\OrderStatus;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public function log(
        AuditAction $action,
        string $description,
        ?Model $subject = null,
        ?Model $causer = null,
        array $properties = [],
        ?Request $request = null,
    ): AuditLog {
        $context = $this->requestContext($request);

        return AuditLog::query()->create([
            'action' => $action,
            'category' => $action->category(),
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'causer_type' => $causer?->getMorphClass(),
            'causer_id' => $causer?->getKey(),
            'properties' => $properties === [] ? null : $properties,
            'ip_address' => $context['ip_address'],
            'user_agent' => $context['user_agent'],
            'browser' => $context['browser'],
        ]);
    }

    /**
     * @param  array{category?: string|null, search?: string|null, action?: string|null}  $filters
     */
    public function paginate(array $filters = [], int $perPage = 30): LengthAwarePaginator
    {
        $query = AuditLog::query()
            ->with(['causer', 'subject'])
            ->latest('created_at');

        if ($category = $filters['category'] ?? null) {
            $query->where('category', $category);
        }

        if ($action = $filters['action'] ?? null) {
            $query->where('action', $action);
        }

        if ($search = $filters['search'] ?? null) {
            $term = '%'.$search.'%';
            $query->where(function ($builder) use ($term): void {
                $builder->where('description', 'like', $term)
                    ->orWhere('ip_address', 'like', $term)
                    ->orWhere('browser', 'like', $term);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function logAdminLogin(User $user, ?Request $request = null): AuditLog
    {
        return $this->log(
            AuditAction::AdminLogin,
            "{$user->name} signed in to the admin panel.",
            causer: $user,
            properties: ['email' => $user->email],
            request: $request,
        );
    }

    public function logAdminLoginFailed(string $email, string $reason, ?Request $request = null): AuditLog
    {
        return $this->log(
            AuditAction::AdminLoginFailed,
            "Failed admin sign-in attempt for {$email}.",
            properties: ['email' => $email, 'reason' => $reason],
            request: $request,
        );
    }

    public function logAdminLogout(?User $user, ?Request $request = null): AuditLog
    {
        return $this->log(
            AuditAction::AdminLogout,
            $user ? "{$user->name} signed out of the admin panel." : 'Admin signed out.',
            causer: $user,
            properties: $user ? ['email' => $user->email] : [],
            request: $request,
        );
    }

    public function logCustomerLogin(Customer $customer, ?Request $request = null): AuditLog
    {
        return $this->log(
            AuditAction::CustomerLogin,
            "{$customer->name} signed in to their account.",
            subject: $customer,
            causer: $customer,
            properties: ['email' => $customer->email],
            request: $request,
        );
    }

    public function logCustomerLogout(?Customer $customer, ?Request $request = null): AuditLog
    {
        return $this->log(
            AuditAction::CustomerLogout,
            $customer ? "{$customer->name} signed out." : 'Customer signed out.',
            subject: $customer,
            causer: $customer,
            properties: $customer ? ['email' => $customer->email] : [],
            request: $request,
        );
    }

    public function logProductCreated(Product $product, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::ProductCreated,
            "Product {$product->name} was created.",
            subject: $product,
            causer: $causer,
            properties: [
                'sku' => $product->sku,
                'status' => $product->status->value ?? $product->status,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    public function logProductUpdated(Product $product, array $changes, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::ProductUpdated,
            "Product {$product->name} was updated.",
            subject: $product,
            causer: $causer,
            properties: ['changes' => $changes],
        );
    }

    public function logProductDeleted(Product $product, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::ProductDeleted,
            "Product {$product->name} was deleted.",
            subject: $product,
            causer: $causer,
        );
    }

    public function logProductRestored(Product $product, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::ProductRestored,
            "Product {$product->name} was restored.",
            subject: $product,
            causer: $causer,
        );
    }

    public function logStockChanged(StockMovement $movement, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();
        $product = $movement->product;

        return $this->log(
            AuditAction::StockChanged,
            "Stock for {$product->name} changed from {$movement->quantity_before} to {$movement->quantity_after}.",
            subject: $product,
            causer: $causer,
            properties: [
                'movement_id' => $movement->id,
                'type' => $movement->type->value,
                'quantity_change' => $movement->quantity_change,
                'warehouse_id' => $movement->warehouse_id,
                'notes' => $movement->notes,
            ],
        );
    }

    public function logOrderStatusUpdated(
        Order $order,
        OrderStatus $previousStatus,
        OrderStatus $status,
        ?string $message = null,
        ?User $causer = null,
    ): AuditLog {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::OrderStatusUpdated,
            "Order {$order->order_number} changed from {$previousStatus->label()} to {$status->label()}.",
            subject: $order,
            causer: $causer,
            properties: [
                'previous_status' => $previousStatus->value,
                'status' => $status->value,
                'message' => $message,
            ],
        );
    }

    public function logOrderNoteAdded(Order $order, string $body, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::OrderNoteAdded,
            "Internal note added to order {$order->order_number}.",
            subject: $order,
            causer: $causer,
            properties: ['note' => $body],
        );
    }

    public function logCustomerCreated(Customer $customer, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::CustomerCreated,
            "Customer {$customer->name} was created.",
            subject: $customer,
            causer: $causer,
            properties: ['email' => $customer->email],
        );
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    public function logCustomerUpdated(Customer $customer, array $changes, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::CustomerUpdated,
            "Customer {$customer->name} was updated.",
            subject: $customer,
            causer: $causer,
            properties: ['changes' => $changes],
        );
    }

    public function logCustomerDeleted(Customer $customer, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::CustomerDeleted,
            "Customer {$customer->name} was deleted.",
            subject: $customer,
            causer: $causer,
        );
    }

    public function logCustomerRestored(Customer $customer, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::CustomerRestored,
            "Customer {$customer->name} was restored.",
            subject: $customer,
            causer: $causer,
        );
    }

    public function logCustomerNoteAdded(Customer $customer, string $body, ?User $causer = null): AuditLog
    {
        $causer ??= $this->adminCauser();

        return $this->log(
            AuditAction::CustomerNoteAdded,
            "Internal note added to customer {$customer->name}.",
            subject: $customer,
            causer: $causer,
            properties: ['note' => $body],
        );
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @param  list<string>  $keys
     * @return array<string, array{from: mixed, to: mixed}>
     */
    public function diffAttributes(array $before, array $after, array $keys): array
    {
        $changes = [];

        foreach ($keys as $key) {
            $from = $before[$key] ?? null;
            $to = $after[$key] ?? null;

            if ($from != $to) {
                $changes[$key] = ['from' => $from, 'to' => $to];
            }
        }

        return $changes;
    }

    private function adminCauser(): ?User
    {
        $user = Auth::guard('admin')->user();

        return $user instanceof User ? $user : null;
    }

    /**
     * @return array{ip_address: ?string, user_agent: ?string, browser: ?string}
     */
    private function requestContext(?Request $request): array
    {
        $request ??= request();

        if (! $request) {
            return [
                'ip_address' => null,
                'user_agent' => null,
                'browser' => null,
            ];
        }

        $userAgent = $request->userAgent();

        return [
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'browser' => $this->parseBrowser($userAgent),
        ];
    }

    private function parseBrowser(?string $userAgent): ?string
    {
        if (! $userAgent) {
            return null;
        }

        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') && ! str_contains($userAgent, 'Chrome/') => 'Safari',
            str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR/') => 'Opera',
            default => 'Other',
        };
    }
}
