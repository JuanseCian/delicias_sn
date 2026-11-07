<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductAdminController;
use App\Http\Controllers\ProductImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/preguntas-frecuentes', FaqController::class)->name('faq');
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/pedidos', [OrderController::class, 'store'])->name('orders.store');
Route::get('/pedidos/{code}/confirmado', [OrderController::class, 'confirmed'])->name('orders.confirmed');

Route::middleware(['auth', 'verified'])->prefix('gestion-delicias-sn-privada')->name('private.')->group(function () {
    Route::get('/productos', [ProductImageController::class, 'index'])->name('products.index');
    Route::get('/productos/nuevo', [ProductAdminController::class, 'create'])->name('products.create');
    Route::post('/productos', [ProductAdminController::class, 'store'])->name('products.store');
    Route::get('/productos/{product}/editar', [ProductAdminController::class, 'edit'])->name('products.edit');
    Route::put('/productos/{product}', [ProductAdminController::class, 'update'])->name('products.update');
    Route::delete('/productos/{product}', [ProductAdminController::class, 'destroy'])->name('products.destroy');
    Route::post('/productos/{product}/imagen', [ProductImageController::class, 'store'])->name('products.image.store');
    Route::delete('/productos/{product}/imagen', [ProductImageController::class, 'destroy'])->name('products.image.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

require __DIR__.'/settings.php';
