<?php

use App\Http\Controllers\Warehouse\CardWarehouseController;
use App\Http\Controllers\Warehouse\ImportController;
use Illuminate\Support\Facades\Route;

Route::prefix('warehouse')->group(function () {
    Route::get('/import', [ImportController::class, 'import'])->name('warehouse.import');
    Route::post('/import', [ImportController::class, 'import'])->name('warehouse.import');
    Route::get('/trash', [ImportController::class, 'importTrash'])->name('warehouse.trash');
    Route::post('/trash', [ImportController::class, 'importTrash'])->name('warehouse.trash');
    Route::get('/create_import', [ImportController::class, 'create_import'])->name('warehouse.create_import');
    Route::post('/create_import', [ImportController::class, 'create_import'])->name('warehouse.create_import');
    Route::post('/store_import', [ImportController::class, 'store_import'])->name('warehouse.store_import');
    Route::post('/import_equipment_request', [ImportController::class, 'import_equipment_request'])->name('warehouse.import_equipment_request');
    Route::get('/edit_import/{code}', [ImportController::class, 'edit_import'])->name('warehouse.edit_import');
    Route::post('/edit_import/{code}', [ImportController::class, 'edit_import'])->name('warehouse.edit_import');
    Route::post('/update_import/{code}', [ImportController::class, 'update_import'])->name('warehouse.update_import');
    Route::post('/approve', [ImportController::class, 'approve'])->name('receipts.approve');
    Route::post('delete', [ImportController::class, 'delete'])->name('receipts.delete');
    Route::get('/export-excel', [ImportController::class, 'exportExcel'])->name('warehouse.exportExcel');
    Route::post('/import-excel', [ImportController::class, 'importExcel'])->name('warehouse.importExcel');
    Route::post('/check-batch-number', [ImportController::class, 'checkBatchNumber'])->name('warehouse.check_batch_number');
    Route::post('/check-receipt-no', [ImportController::class, 'checkReceiptNo'])->name('warehouse.check_receipt_no');
});
