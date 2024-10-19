<?php

use App\Http\Controllers\Warehouse\CheckWarehouseController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckLogin;

Route::prefix('check_warehouse')->middleware(CheckLogin::class)->group(function () {
    Route::get('/', [CheckWarehouseController::class, 'index'])->name('check_warehouse.index');

    Route::get('/create', [CheckWarehouseController::class, 'create'])->name('check_warehouse.create');

    Route::post('/store', [CheckWarehouseController::class, 'store'])->name('check_warehouse.store');

    Route::get('/search-import', [CheckWarehouseController::class, 'search'])->name('check_warehouse.search');

    Route::post('/approve/{code}', [CheckWarehouseController::class, 'approveCheck'])->name('check_warehouse.approve');

    Route::post('check-warehouse/cancel/{code}', [CheckWarehouseController::class, 'cancelCheck'])->name('check_warehouse.cancel');

    Route::post('/check-warehouse/delete/{code}', [CheckWarehouseController::class, 'deleteCheck'])->name('check_warehouse.delete');

    Route::get('/inventory-edit/{code}', [CheckWarehouseController::class, 'edit'])->name('inventory_check.edit');

    Route::post('/inventory-update/{code}', [CheckWarehouseController::class, 'update'])->name('inventory_check.update');

    Route::get('/checkwarehouse-excel-export', [CheckWarehouseController::class, 'exportCheckWarehouseExcel'])->name('warehouse.exportCheckWarehouseExcel');

    Route::post('/checkwarehouse-excel-import', [CheckWarehouseController::class, 'importCheckWarehouseExcel'])->name('warehouse.importCheckWarehouseExcel');
});