<?php

use App\Http\Controllers\Web\BackEnd\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/item', [ItemController::class, 'index'])->name('be.item.index');
Route::get('/item/create', [ItemController::class, 'create'])->name('be.item.create');
Route::post('/item/store', [ItemController::class, 'store'])->name('be.item.store');
Route::get('/item/{item:code}', [ItemController::class, 'edit'])->name('be.item.edit');
Route::put('/item/{item:code}', [ItemController::class, 'update'])->name('be.item.update');
Route::delete('/item/{item:code}', [ItemController::class, 'destroy'])->name('be.item.destroy');
Route::get('/item/mass/destroy', [ItemController::class, 'massDestroy'])->name('be.item.mass.destroy');
