<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // Dashboard
    case READ_DASHBOARD = 'dashboard_read';

    // Users
    case CREATE_USER = 'users_create';
    case READ_USER = 'users_read';
    case UPDATE_USER = 'users_update';
    case DELETE_USER = 'users_delete';

    // Roles
    case CREATE_ROLE = 'roles_create';
    case READ_ROLE = 'roles_read';
    case UPDATE_ROLE = 'roles_update';
    case DELETE_ROLE = 'roles_delete';
    case UPDATE_ROLE_PERMISSION = 'roles_update_permission';

    // Sales
    case CREATE_SALE = 'sales_create';
    case READ_SALE = 'sales_read';
    case UPDATE_SALE = 'sales_update';
    case DELETE_SALE = 'sales_delete';

    // Payments
    case CREATE_PAYMENT = 'payments_create';
    case READ_PAYMENT = 'payments_read';
    case UPDATE_PAYMENT = 'payments_update';
    case DELETE_PAYMENT = 'payments_delete';

    // Items
    case CREATE_ITEM = 'items_create';
    case READ_ITEM = 'items_read';
    case UPDATE_ITEM = 'items_update';
    case DELETE_ITEM = 'items_delete';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
