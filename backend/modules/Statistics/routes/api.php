<?php

use Illuminate\Support\Facades\Route;
use Modules\Statistics\Http\Controllers\ManagerDefaultStatsController;

Route::prefix('management/statistics')->middleware('auth.manager')->group(function () {
    Route::get('/general-home', ManagerDefaultStatsController::class);
});
