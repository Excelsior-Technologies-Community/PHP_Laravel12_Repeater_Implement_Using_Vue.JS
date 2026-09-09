<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;

Route::redirect('/', '/product-dashboard');

/*
|--------------------------------------------------------------------------
| Product Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/product-dashboard', [ProductDashboardController::class, 'index'])->name('product.dashboard');

/*
|--------------------------------------------------------------------------
| Product Resource & Extended Actions
|--------------------------------------------------------------------------
*/
Route::match(['post', 'delete'], '/products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('product.bulk-destroy');
Route::get('/products/export/csv', [ProductController::class, 'exportCsv'])->name('product.export');
Route::post('/product/{product}/duplicate', [ProductController::class, 'duplicate'])->name('product.duplicate');
Route::post('/product/{product}/set-primary/{image}', [ProductController::class, 'setPrimary'])->name('product.image.primary');
Route::post('/product/{product}/reorder-images', [ProductController::class, 'reorderImages'])->name('product.image.reorder');
Route::delete('/product/{product}/image/{image}', [ProductController::class, 'destroyImage'])->name('product.image.destroy');

Route::resource('product', ProductController::class);

/*
|--------------------------------------------------------------------------
| Master Modules: Category & Brand
|--------------------------------------------------------------------------
*/
Route::resource('category', CategoryController::class);
Route::resource('brand', BrandController::class);
