<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Users\Http\Controllers\GuestsController;
use Modules\User\Users\Http\Controllers\UserController;

Route::prefix('management/users')->middleware('auth.manager')->group(function () {
    Route::get('/regular-customers', [UserController::class, 'getRegularCustomers']);

    Route::get('/guests', [GuestsController::class, 'getGuests']);
});