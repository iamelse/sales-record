<?php

namespace App\Enums;

enum SaleStatus: string
{
    case UNPAID = 'Belum Dibayar';
    case PARTIALLY_PAID = 'Belum Dibayar Sepenuhnya';
    case PAID = 'Sudah Dibayar';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
