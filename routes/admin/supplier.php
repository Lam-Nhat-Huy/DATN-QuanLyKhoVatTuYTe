<?php

use App\Http\Controllers\Supplier\SupplierController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckLogin;

Route::prefix('supplier')->middleware(CheckLogin::class)->group(function () {

    Route::get('/', [SupplierController::class, 'index'])->name('supplier.list');

    Route::post('/', [SupplierController::class, 'index'])->name('supplier.list');

    Route::get('/trash', [SupplierController::class, 'trash'])->name('supplier.trash');

    Route::get('/quote_history', [SupplierController::class, 'quote_history'])->name('supplier.quote_history');
    
    Route::post('/trash', [SupplierController::class, 'trash'])->name('supplier.trash');

    Route::get('/add', [SupplierController::class, 'add'])->name('supplier.add');

    Route::post('/create', [SupplierController::class, 'create'])->name('supplier.create');

    Route::get('/edit/{code}', [SupplierController::class, 'edit'])->name('supplier.edit');

    Route::post('/update', [SupplierController::class, 'update'])->name('supplier.update');

    Route::get('/suppliers/search', [SupplierController::class, 'search'])->name('supplier.search');

});
