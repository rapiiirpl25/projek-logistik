<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogisticTaskController;

// Auth Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [LogisticTaskController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/task/create', [LogisticTaskController::class, 'create'])->name('task.create');
        Route::post('/task/store', [LogisticTaskController::class, 'store'])->name('task.store');
        Route::post('/task/{id}/review', [LogisticTaskController::class, 'reviewTask'])->name('task.review');
        
        Route::get('/operator/create', [LogisticTaskController::class, 'createOperator'])->name('operator.create');
        Route::post('/operator/store', [LogisticTaskController::class, 'storeOperator'])->name('operator.store');
        Route::get('/operator/{id}/edit', [LogisticTaskController::class, 'editOperator'])->name('operator.edit');
        Route::put('/operator/{id}', [LogisticTaskController::class, 'updateOperator'])->name('operator.update');
    });

    Route::prefix('operator')->name('operator.')->group(function () {
        Route::get('/dashboard', [LogisticTaskController::class, 'operatorDashboard'])->name('dashboard');
        Route::post('/task/{id}/start', [LogisticTaskController::class, 'startTask'])->name('task.start');
        Route::post('/task/{id}/report', [LogisticTaskController::class, 'submitReport'])->name('task.report');
    });

    Route::get('/report/print', [LogisticTaskController::class, 'printReport'])->name('report.print');

    Route::get('/operator/inventory/incoming', [App\Http\Controllers\LogisticTaskController::class, 'createIncomingReport'])->name('operator.inventory.incoming');
Route::post('/operator/inventory/incoming', [App\Http\Controllers\LogisticTaskController::class, 'storeIncomingReport'])->name('operator.inventory.store');
});