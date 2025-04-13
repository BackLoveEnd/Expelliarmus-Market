<?php

use Illuminate\Support\Facades\Route;
use Modules\ContentManagement\Http\Controllers\NewArrivals\NewArrivalsContentController;
use Modules\ContentManagement\Http\Controllers\Slider\SliderContentController;

Route::prefix('management/content')->group(function () {
    Route::prefix('slider')->controller(SliderContentController::class)->group(function () {
        Route::get('/', 'getAllSliderContent')->withoutMiddleware('throttle:api');

        Route::post('/', 'upload')->middleware('auth.manager');

        Route::delete('/slides/{slide:slide_id}', 'deleteSlide')
            ->whereUuid('slide')
            ->middleware('auth.manager');
    });

    Route::prefix('new-arrivals')->controller(NewArrivalsContentController::class)->group(function () {
        Route::get('/', 'getNewArrivals')->withoutMiddleware('throttle:api');

        Route::post('/', 'uploadNewArrivals')->middleware('auth.manager');

        Route::delete('/{arrival:arrival_id}', 'deleteArrival')
            ->middleware('auth.manager')
            ->whereUuid('arrival');
    });
});
