<?php

use App\Http\Controllers\Web\BackEnd\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('/payment')->name('be.payment.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    // Route::get('/create', [PaymentController::class, 'create'])->name('create');
    // Route::post('/store', [PaymentController::class, 'store'])->name('store');
    Route::get('/pay/{sale:code}', [PaymentController::class, 'create'])->name('create');
    Route::post('/pay/{sale:code}/store', [PaymentController::class, 'store'])->name('store');
    Route::get('/{payment:code}', [PaymentController::class, 'show'])->name('show');
    Route::get('/{payment:code}/edit', [PaymentController::class, 'edit'])->name('edit');
    Route::put('/{payment:code}', [PaymentController::class, 'update'])->name('update');
    Route::delete('/{payment:code}', [PaymentController::class, 'destroy'])->name('destroy');
    Route::get('/mass/destroy', [PaymentController::class, 'massDestroy'])->name('mass.destroy');
});
