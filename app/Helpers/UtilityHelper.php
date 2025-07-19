<?php

use App\Models\Item;
use Illuminate\Support\Str;

if (!function_exists('generateUniqueItemCode')) {
    function generateUniqueItemCode(int $letterCount = 2, int $numberCount = 5): string
    {
        do {
            $letters = strtoupper(Str::random($letterCount));
            $numbers = str_pad((string)random_int(0, pow(10, $numberCount) - 1), $numberCount, '0', STR_PAD_LEFT);
            $code = "ITEM-{$letters}-{$numbers}";
        } while (Item::where('code', $code)->exists());

        return $code;
    }
}
