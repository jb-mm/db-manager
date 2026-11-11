<?php

use Illuminate\Support\Facades\Route;
use Jbmm\DbTableManager\Http\Controllers\DbTableManagerController;

Route::middleware(config('db-manager.middleware', ['web', 'db-manager.enabled']))
    ->prefix(config('db-manager.route_prefix', 'db-manager'))
    ->group(function () {
        Route::get('/', [DbTableManagerController::class, 'index'])->name('db-manager.index');
        Route::post('/tables/{table}', [DbTableManagerController::class, 'store'])->name('db-manager.store');
        Route::put('/tables/{table}/{pk}', [DbTableManagerController::class, 'update'])->name('db-manager.update');
        Route::delete('/tables/{table}/{pk}', [DbTableManagerController::class, 'destroy'])->name('db-manager.destroy');
    });
