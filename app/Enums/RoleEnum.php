<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'Admin';
    case CASHIER = 'Cashier';
    case MANAGER = 'Manager';

    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => [
                // Dashboard
                PermissionEnum::READ_DASHBOARD,

                // Users
                PermissionEnum::CREATE_USER,
                PermissionEnum::READ_USER,
                PermissionEnum::UPDATE_USER,
                PermissionEnum::DELETE_USER,

                // Roles
                PermissionEnum::CREATE_ROLE,
                PermissionEnum::READ_ROLE,
                PermissionEnum::UPDATE_ROLE,
                PermissionEnum::DELETE_ROLE,
                PermissionEnum::UPDATE_ROLE_PERMISSION,

                // Sales
                PermissionEnum::CREATE_SALE,
                PermissionEnum::READ_SALE,
                PermissionEnum::UPDATE_SALE,
                PermissionEnum::DELETE_SALE,

                // Payments
                PermissionEnum::CREATE_PAYMENT,
                PermissionEnum::READ_PAYMENT,
                PermissionEnum::UPDATE_PAYMENT,
                PermissionEnum::DELETE_PAYMENT,

                // Items
                PermissionEnum::CREATE_ITEM,
                PermissionEnum::READ_ITEM,
                PermissionEnum::UPDATE_ITEM,
                PermissionEnum::DELETE_ITEM,
            ],

            self::CASHIER => [
                PermissionEnum::READ_DASHBOARD,

                // Sales
                PermissionEnum::CREATE_SALE,
                PermissionEnum::READ_SALE,
                PermissionEnum::UPDATE_SALE,
                PermissionEnum::DELETE_SALE,

                // Payments
                PermissionEnum::CREATE_PAYMENT,
                PermissionEnum::READ_PAYMENT,
                PermissionEnum::UPDATE_PAYMENT,
                PermissionEnum::DELETE_PAYMENT,

                // Items (Read-only)
                PermissionEnum::READ_ITEM,
            ],

            self::MANAGER => [
                PermissionEnum::READ_DASHBOARD,
                PermissionEnum::READ_SALE,
                PermissionEnum::READ_PAYMENT,
                PermissionEnum::READ_ITEM,
            ],
        };
    }
}
