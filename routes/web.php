<?php

use App\Http\Controllers\PollController;
use App\Http\Controllers\ProductBulkImportController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('index');

Route::get('/products/get-products-list', [ProductController::class, 'getProductsList' ])->name('get-products-list');

Route::post('/products/bulk-upload-product-csv', [ProductBulkImportController::class, 'bulkUploadProductsViaCsv'])->name('bulk-upload-product-csv');
Route::get('/import-status/{batchId}', [PollController::class, 'getImportStatus'])->name('import-status');
Route::get('/import-list', [ProductController::class, 'importListPage'])->name('import-list-page');
Route::get('/products/get-imports', [ProductController::class, 'getImports'])->name('get-imports');
