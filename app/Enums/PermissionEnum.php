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

    // Penjualan
    case CREATE_PENJUALAN = 'penjualan_create';
    case READ_PENJUALAN = 'penjualan_read';
    case UPDATE_PENJUALAN = 'penjualan_update';
    case DELETE_PENJUALAN = 'penjualan_delete';

    // Pembayaran
    case CREATE_PEMBAYARAN = 'pembayaran_create';
    case READ_PEMBAYARAN = 'pembayaran_read';
    case UPDATE_PEMBAYARAN = 'pembayaran_update';
    case DELETE_PEMBAYARAN = 'pembayaran_delete';

    // Item
    case CREATE_ITEM = 'item_create';
    case READ_ITEM = 'item_read';
    case UPDATE_ITEM = 'item_update';
    case DELETE_ITEM = 'item_delete';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
