<?php

namespace App\Enums;

enum AuditAction: string
{
    case AdminLogin = 'admin.login';
    case AdminLoginFailed = 'admin.login_failed';
    case AdminLogout = 'admin.logout';
    case CustomerLogin = 'customer.login';
    case CustomerLogout = 'customer.logout';
    case ProductCreated = 'product.created';
    case ProductUpdated = 'product.updated';
    case ProductDeleted = 'product.deleted';
    case ProductRestored = 'product.restored';
    case StockChanged = 'inventory.stock_changed';
    case OrderStatusUpdated = 'order.status_updated';
    case OrderNoteAdded = 'order.note_added';
    case CustomerCreated = 'customer.created';
    case CustomerUpdated = 'customer.updated';
    case CustomerDeleted = 'customer.deleted';
    case CustomerRestored = 'customer.restored';
    case CustomerNoteAdded = 'customer.note_added';

    public function label(): string
    {
        return match ($this) {
            self::AdminLogin => 'Admin signed in',
            self::AdminLoginFailed => 'Admin sign-in failed',
            self::AdminLogout => 'Admin signed out',
            self::CustomerLogin => 'Customer signed in',
            self::CustomerLogout => 'Customer signed out',
            self::ProductCreated => 'Product created',
            self::ProductUpdated => 'Product updated',
            self::ProductDeleted => 'Product deleted',
            self::ProductRestored => 'Product restored',
            self::StockChanged => 'Stock changed',
            self::OrderStatusUpdated => 'Order status updated',
            self::OrderNoteAdded => 'Order note added',
            self::CustomerCreated => 'Customer created',
            self::CustomerUpdated => 'Customer updated',
            self::CustomerDeleted => 'Customer deleted',
            self::CustomerRestored => 'Customer restored',
            self::CustomerNoteAdded => 'Customer note added',
        };
    }

    public function category(): AuditCategory
    {
        return match ($this) {
            self::AdminLogin, self::AdminLoginFailed, self::AdminLogout,
            self::CustomerLogin, self::CustomerLogout => AuditCategory::Auth,
            self::ProductCreated, self::ProductUpdated, self::ProductDeleted, self::ProductRestored => AuditCategory::Product,
            self::StockChanged => AuditCategory::Inventory,
            self::OrderStatusUpdated, self::OrderNoteAdded => AuditCategory::Order,
            self::CustomerCreated, self::CustomerUpdated, self::CustomerDeleted,
            self::CustomerRestored, self::CustomerNoteAdded => AuditCategory::Customer,
        };
    }
}
