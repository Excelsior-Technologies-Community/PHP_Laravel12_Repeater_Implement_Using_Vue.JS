<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::resource('product', ProductController::class);

Route::post(
    '/product/{product}/set-primary/{image}',
    [ProductController::class, 'setPrimary']
)->name('product.image.primary');

Route::post(
    '/product/{product}/reorder-images',
    [ProductController::class, 'reorderImages']
)->name('product.image.reorder');