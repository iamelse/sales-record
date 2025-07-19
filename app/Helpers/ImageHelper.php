<?php

use App\Enums\FileSystemDiskEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('resolveImagePath')) {
    function resolveImagePath(?string $imagePath, ?string $fallbackBase64 = null): string
    {
        $disk = env('FILESYSTEM_DISK');
        $appUrl = rtrim(env('APP_URL'), '/');
        $publicHtmlPath = base_path('../public_html');

        if (!$imagePath) {
            return $fallbackBase64 ?? 'https://dummyimage.com/300';
        }

        return match ($disk) {
            FileSystemDiskEnum::PUBLIC->value =>
            Storage::disk('public')->exists($imagePath)
                ? asset('storage/' . $imagePath)
                : ($fallbackBase64 ?? 'https://dummyimage.com/300'),

            FileSystemDiskEnum::PUBLIC_UPLOADS->value =>
            file_exists($publicHtmlPath . '/' . $imagePath)
                ? $appUrl . '/' . $imagePath
                : ($fallbackBase64 ?? 'https://dummyimage.com/300'),

            default => $fallbackBase64 ?? 'https://dummyimage.com/300',
        };
    }
}

if (!function_exists('getUserImageProfilePath')) {
    function getUserImageProfilePath($user)
    {
        $avatar = Avatar::create(Auth::user()->name)->toBase64();
        return resolveImagePath($user->image, $avatar);
    }
}

if (!function_exists('getItemImagePath')) {
    function getItemImagePath($item)
    {
        $fallback = "https://dummyimage.com/300";
        return resolveImagePath($item->image, $fallback);
    }
}
