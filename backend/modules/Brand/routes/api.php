<?php

use Illuminate\Support\Facades\Route;
use Modules\Brand\Http\Controllers\ManipulationBrandController;
use Modules\Brand\Http\Controllers\RetrieveBrandsController;


Route::prefix('management/brands')->group(function () {
    Route::get('/', [RetrieveBrandsController::class, 'getPaginated']);

    Route::controller(ManipulationBrandController::class)->group(function () {
        Route::post('/', 'create');

        Route::put('/{brand}', 'edit')->whereNumber('brand');

        Route::delete('/{brand}', 'delete')->whereNumber('brand');
    });
});