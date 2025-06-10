<?php

use Illuminate\Support\Facades\Route;
use Modules\Manager\Http\Controllers\Auth\ManagerAuthController;
use Modules\Manager\Http\Controllers\RetrieveManagersController;

Route::prefix('management/managers')->group(function () {
    Route::controller(ManagerAuthController::class)->prefix('auth')->group(function () {
        Route::get('/me', 'manager')
            ->middleware('auth.manager');

        Route::post('/register', 'register')->middleware('auth.manager:super-manager');

        Route::post('/login', 'login')->middleware('guest.manager');

        Route::post('/logout', 'logout')
            ->middleware('auth.manager')
            ->name('manager.logout');
    });

    Route::get('/', [RetrieveManagersController::class, 'retrieveManagerTable'])->middleware(
        'auth.manager:super-manager',
    );
});
