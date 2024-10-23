<?php

use App\Http\Controllers\EquipmentRequest\EquipmentRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckLogin;


Route::prefix('equipment_request')->middleware(CheckLogin::class)->name('equipment_request.')->group(function () {

    // Nhập
    Route::get('/import', [EquipmentRequestController::class, 'import_equipment_request'])->name('import');
    Route::post('/import', [EquipmentRequestController::class, 'import_equipment_request'])->name('import');
    Route::get('/import_trash', [EquipmentRequestController::class, 'import_equipment_request_trash'])->name('import_trash');
    Route::post('/import_trash', [EquipmentRequestController::class, 'import_equipment_request_trash'])->name('import_trash');
    Route::get('/create_import', [EquipmentRequestController::class, 'create_import_equipment_request'])->name('create_import');
    Route::post('/create_import', [EquipmentRequestController::class, 'create_import_equipment_request'])->name('create_import');
    Route::post('/delete_supplier/{code}', [EquipmentRequestController::class, 'delete_supplier'])->name('delete_supplier');
    Route::post('/store_import', [EquipmentRequestController::class, 'store_import_equipment_request'])->name('store_import');
    Route::get('/update_import/{code}', [EquipmentRequestController::class, 'update_import_equipment_request'])->name('update_import');
    Route::post('/edit_import/{code}', [EquipmentRequestController::class, 'edit_import_equipment_request'])->name('edit_import');
    Route::post('/edit_price/{code}', [EquipmentRequestController::class, 'edit_import_equipment_request_price'])->name('edit_import_price');
    Route::get('/exportExcelEquipmentRequestList/{code}', [EquipmentRequestController::class, 'exportExcelEquipmentRequestList'])->name('exportExcelEquipmentRequestList');

    // Xuất
    Route::get('/export', [EquipmentRequestController::class, 'export_equipment_request'])->name('export');
    Route::post('/export', [EquipmentRequestController::class, 'export_equipment_request'])->name('export');
    Route::get('/export_trash', [EquipmentRequestController::class, 'export_equipment_request_trash'])->name('export_trash');
    Route::post('/export_trash', [EquipmentRequestController::class, 'export_equipment_request_trash'])->name('export_trash');
    Route::get('/create_export', [EquipmentRequestController::class, 'create_export_equipment_request'])->name('create_export');
    Route::post('/create_export', [EquipmentRequestController::class, 'create_export_equipment_request'])->name('create_export');
    Route::post('/delete_department/{code}', [EquipmentRequestController::class, 'delete_department'])->name('delete_department');
    Route::post('/store_export', [EquipmentRequestController::class, 'store_export_equipment_request'])->name('store_export');
    Route::get('/update_export/{code}', [EquipmentRequestController::class, 'update_export_equipment_request'])->name('update_export');
    Route::post('/edit_export/{code}', [EquipmentRequestController::class, 'edit_export_equipment_request'])->name('edit_export');
});
