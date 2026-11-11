<?php

use Illuminate\Support\Facades\Route;
use Jb\DbManager\Http\Controllers\DbManagerController;

Route::middleware(config('db-manager.middleware', ['web', 'db-manager.enabled']))
    ->prefix(config('db-manager.route_prefix', 'db-manager'))
    ->group(function () {
        Route::get('/', [DbManagerController::class, 'index'])->name('db-manager.index');
        Route::post('/tables/{table}', [DbManagerController::class, 'store'])->name('db-manager.store');
        Route::put('/tables/{table}/{pk}', [DbManagerController::class, 'update'])->name('db-manager.update');
        Route::delete('/tables/{table}/{pk}', [DbManagerController::class, 'destroy'])->name('db-manager.destroy');
    });
