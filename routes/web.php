<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::resource('product', ProductController::class);

/*
|--------------------------------------------------------------------------
| Product Image Routes
|--------------------------------------------------------------------------
*/

Route::post(
    '/product/{product}/set-primary/{image}',
    [ProductController::class, 'setPrimary']
)->name('product.image.primary');

Route::post(
    '/product/{product}/reorder-images',
    [ProductController::class, 'reorderImages']
)->name('product.image.reorder');

/*
|--------------------------------------------------------------------------
| Feature 7 - Delete Individual Image
|--------------------------------------------------------------------------
*/

Route::delete(
    '/product/{product}/image/{image}',
    [ProductController::class, 'destroyImage']
)->name('product.image.destroy');

/*
|--------------------------------------------------------------------------
| Feature 8 - Bulk Delete
|--------------------------------------------------------------------------
*/

Route::delete(
    '/products/bulk-delete',
    [ProductController::class, 'bulkDestroy']
)->name('product.bulk.destroy');

/*
|--------------------------------------------------------------------------
| Feature 9 - CSV Export
|--------------------------------------------------------------------------
*/

Route::get(
    '/products/export/csv',
    [ProductController::class, 'exportCsv']
)->name('product.export.csv');

/*
|--------------------------------------------------------------------------
| Feature 10 - Duplicate Product
|--------------------------------------------------------------------------
*/

Route::post(
    '/product/{product}/duplicate',
    [ProductController::class, 'duplicate']
)->name('product.duplicate');