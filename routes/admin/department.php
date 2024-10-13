<?php

use App\Http\Controllers\Department\DepartmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckLogin;

Route::prefix('department')->middleware(CheckLogin::class)->group(function () {

    Route::get('/', [DepartmentController::class, 'index'])->name('department.index');

    Route::post('/', [DepartmentController::class, 'index'])->name('department.index');

    Route::get('/trash', [DepartmentController::class, 'trash'])->name('department.trash');
    
    Route::post('/trash', [DepartmentController::class, 'trash'])->name('department.trash');

    Route::get('/add', [DepartmentController::class, 'add'])->name('department.add');

    Route::post('/create', [DepartmentController::class, 'create'])->name('department.create');

    Route::get('/edit/{code}', [DepartmentController::class, 'edit'])->name('department.edit');

    Route::post('/update', [DepartmentController::class, 'update'])->name('department.update');

    Route::get('/departments/search', [DepartmentController::class, 'search'])->name('department.search');

});
