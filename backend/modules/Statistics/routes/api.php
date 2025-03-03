<?php

use Illuminate\Support\Facades\Route;
use Modules\Statistics\Http\Controllers\ManagerDefaultStatsController;

Route::prefix('management/statistics')->group(function () {
    Route::get('/general-home', ManagerDefaultStatsController::class);
});
