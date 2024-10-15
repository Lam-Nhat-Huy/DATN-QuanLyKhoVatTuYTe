<?php

use App\Http\Controllers\Warehouse\CardWarehouseController;
use App\Http\Controllers\Warehouse\ExportController;
use Illuminate\Support\Facades\Route;

Route::prefix('warehouse')->group(function () {
    Route::get('/export', [ExportController::class, 'export'])->name('warehouse.export');
    Route::get('/create_export', [ExportController::class, 'create_export'])->name('warehouse.create_export');
    Route::post('/create_export', [ExportController::class, 'create_export'])->name('warehouse.post_export');
    Route::post('/store_export', [ExportController::class, 'store_export'])->name('warehouse.store_export');
    Route::post('/approve_export', [ExportController::class, 'approve_export'])->name('warehouse.approve_export');
    Route::get('/inventory', [ExportController::class, 'inventory'])->name('warehouse.inventory');
    Route::post('/add-equipment', [ExportController::class, 'add_equipment_to_list'])->name('warehouse.add_equipment_to_list');
});
