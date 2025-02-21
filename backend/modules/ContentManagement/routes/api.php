<?php

use Illuminate\Support\Facades\Route;
use Modules\ContentManagement\Http\Controllers\NewArrivals\NewArrivalsContentController;
use Modules\ContentManagement\Http\Controllers\Slider\SliderContentController;

Route::prefix('management/content')->group(function () {
    Route::prefix('slider')->controller(SliderContentController::class)->group(function () {
        Route::get('/', 'getAllSliderContent');

        Route::post('/', 'upload');

        Route::delete('/slides/{slide:slide_id}', 'deleteSlide')
            ->whereUuid('slide');
    });

    Route::prefix('new-arrivals')->controller(NewArrivalsContentController::class)->group(function () {
        Route::get('/', 'getNewArrivals');

        Route::post('/', 'uploadNewArrivals');

        Route::delete('/{arrival:arrival_id}', 'deleteArrival')
            ->whereUuid('arrival');
    });
});
