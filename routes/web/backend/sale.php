<?php

use App\Http\Controllers\Web\BackEnd\SaleController;
use Illuminate\Support\Facades\Route;

Route::prefix('/sale')->name('be.sale.')->group(function () {
    Route::get('/', [SaleController::class, 'index'])->name('index');
    Route::get('/create', [SaleController::class, 'create'])->name('create');
    Route::post('/store', [SaleController::class, 'store'])->name('store');
    Route::get('/{sale:code}', [SaleController::class, 'show'])->name('show');
    Route::get('/{sale:code}/edit', [SaleController::class, 'edit'])->name('edit');
    Route::put('/{sale:code}', [SaleController::class, 'update'])->name('update');
    Route::delete('/{sale:code}', [SaleController::class, 'destroy'])->name('destroy');
    Route::get('/mass/destroy', [SaleController::class, 'massDestroy'])->name('mass.destroy');
});
