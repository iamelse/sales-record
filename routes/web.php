<?php

use Illuminate\Support\Facades\Route;

/**
 * Web routes auth
 */
Route::prefix('auth')->group(function () {
    include __DIR__.'/web/auth/auth.php';
});

/**
 * Web routes backend
 */
Route::prefix('admin')->middleware('is.auth')->group(function () {
    include __DIR__ .'/web/backend/dashboard.php';

    include __DIR__ . '/web/backend/role.php';
    include __DIR__ . '/web/backend/user.php';

    include __DIR__ . '/web/backend/user-profile.php';
});
