<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\GuestsController;
use Modules\User\Http\Controllers\UserController;

Route::prefix('management/users')->middleware('auth.manager')->group(function () {
    Route::get('/regular-customers', [UserController::class, 'getRegularCustomers']);

    Route::get('/guests', [GuestsController::class, 'getGuests']);
});