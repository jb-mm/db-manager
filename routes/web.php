<?php

use Illuminate\Support\Facades\Route;
use Jbmm\TableRecordManager\Http\Controllers\TableRecordManagerController;

Route::middleware(config('db-manager.middleware', ['web', 'db-manager.enabled']))
    ->prefix(config('db-manager.route_prefix', 'db-manager'))
    ->group(function () {
        Route::get('/', [TableRecordManagerController::class, 'index'])->name('db-manager.index');
        Route::post('/tables/{table}', [TableRecordManagerController::class, 'store'])->name('db-manager.store');
        Route::put('/tables/{table}/{pk}', [TableRecordManagerController::class, 'update'])->name('db-manager.update');
        Route::delete('/tables/{table}/{pk}', [TableRecordManagerController::class, 'destroy'])->name('db-manager.destroy');
    });
