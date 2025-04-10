<?php

use Illuminate\Support\Facades\Route;
use Modules\Manager\Http\Controllers\Auth\ManagerAuthController;

Route::prefix('management/managers')->group(function () {
    Route::controller(ManagerAuthController::class)->prefix('auth')->group(function () {
        Route::post('/register', 'register')->middleware('auth.manager:super-manager');

        Route::post('/login', 'login')->middleware('guest.manager');

        Route::post('/logout', 'logout')
            ->middleware('auth.manager')
            ->name('manager.logout');
    });
});