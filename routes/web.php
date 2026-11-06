<?php

use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/preguntas-frecuentes', FaqController::class)->name('faq');
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/pedidos', [OrderController::class, 'store'])->name('orders.store');
Route::get('/pedidos/{code}/confirmado', [OrderController::class, 'confirmed'])->name('orders.confirmed');

Route::prefix('gestion-delicias-sn-privada')->name('private.')->group(function () {
    Route::get('/productos', [ProductImageController::class, 'index'])->name('products.index');
    Route::post('/productos/{product}/imagen', [ProductImageController::class, 'store'])->name('products.image.store');
    Route::delete('/productos/{product}/imagen', [ProductImageController::class, 'destroy'])->name('products.image.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
