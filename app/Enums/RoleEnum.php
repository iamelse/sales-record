<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'Admin';
    case CASHIER = 'Cashier';
    case MANAGER = 'Manager'; // Tambahan (opsional)

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

                // Penjualan
                PermissionEnum::CREATE_PENJUALAN,
                PermissionEnum::READ_PENJUALAN,
                PermissionEnum::UPDATE_PENJUALAN,
                PermissionEnum::DELETE_PENJUALAN,

                // Pembayaran
                PermissionEnum::CREATE_PEMBAYARAN,
                PermissionEnum::READ_PEMBAYARAN,
                PermissionEnum::UPDATE_PEMBAYARAN,
                PermissionEnum::DELETE_PEMBAYARAN,

                // Item
                PermissionEnum::CREATE_ITEM,
                PermissionEnum::READ_ITEM,
                PermissionEnum::UPDATE_ITEM,
                PermissionEnum::DELETE_ITEM,
            ],

            self::CASHIER => [
                PermissionEnum::READ_DASHBOARD,

                PermissionEnum::CREATE_PENJUALAN,
                PermissionEnum::READ_PENJUALAN,
                PermissionEnum::UPDATE_PENJUALAN,
                PermissionEnum::DELETE_PENJUALAN,

                PermissionEnum::CREATE_PEMBAYARAN,
                PermissionEnum::READ_PEMBAYARAN,
                PermissionEnum::UPDATE_PEMBAYARAN,
                PermissionEnum::DELETE_PEMBAYARAN,

                PermissionEnum::READ_ITEM,
            ],

            self::MANAGER => [ // Tambahan opsional untuk read-only user
                PermissionEnum::READ_DASHBOARD,
                PermissionEnum::READ_PENJUALAN,
                PermissionEnum::READ_PEMBAYARAN,
                PermissionEnum::READ_ITEM,
            ],
        };
    }
}
