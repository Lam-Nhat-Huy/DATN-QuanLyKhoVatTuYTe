<?php

use App\Http\Controllers\Warehouse\CardWarehouseController;
use App\Http\Controllers\Warehouse\ImportController;
use Illuminate\Support\Facades\Route;

Route::prefix('warehouse')->group(function () {
    Route::get('/import', [ImportController::class, 'import'])->name('warehouse.import');
    Route::get('/create_import', [ImportController::class, 'create_import'])->name('warehouse.create_import');
    Route::post('/store_import', [ImportController::class, 'store_import'])->name('warehouse.store_import');
});