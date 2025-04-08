<?php

use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;
use Modules\ContentManagement\Http\Controllers\NewArrivals\NewArrivalsContentController;
use Modules\ContentManagement\Http\Controllers\Slider\SliderContentController;

Route::prefix('management/content')->group(function () {
    Route::prefix('slider')->controller(SliderContentController::class)->group(function () {
        Route::get('/', 'getAllSliderContent')->withoutMiddleware(ThrottleRequests::class);

        Route::post('/', 'upload');

        Route::delete('/slides/{slide:slide_id}', 'deleteSlide')
            ->whereUuid('slide');
    });

    Route::prefix('new-arrivals')->controller(NewArrivalsContentController::class)->group(function () {
        Route::get('/', 'getNewArrivals')->withoutMiddleware('throttle:api');

        Route::post('/', 'uploadNewArrivals');

        Route::delete('/{arrival:arrival_id}', 'deleteArrival')
            ->whereUuid('arrival');
    });
});
