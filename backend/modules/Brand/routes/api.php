<?php

use Illuminate\Support\Facades\Route;
use Modules\Brand\Http\Controllers\BrandLogoController;
use Modules\Brand\Http\Controllers\ManipulationBrandController;
use Modules\Brand\Http\Controllers\RelatedProductBrandsController;
use Modules\Brand\Http\Controllers\RetrieveBrandsController;

Route::prefix('management/brands')->middleware(['auth.manager', 'role:manager'])->group(function () {
    Route::get('/', [RetrieveBrandsController::class, 'getPaginated'])->withoutMiddleware('throttle:api');

    Route::get('/{brandId}', [RetrieveBrandsController::class, 'getBrandInfo'])->withoutMiddleware('throttle:api');

    Route::controller(ManipulationBrandController::class)->group(function () {
        Route::post('/', 'create');

        Route::put('/{brand}', 'edit')->whereNumber('brand');

        Route::delete('/{brand}', 'delete')->whereNumber('brand');
    });

    Route::controller(BrandLogoController::class)->group(function () {
        Route::post('/logo/{brand}', 'upload')->whereNumber('brand');
    });
});

Route::get(
    '/shop/products/categories/{category}/brands',
    [RelatedProductBrandsController::class, 'getProductBrandsByCategory'],
)
    ->withoutMiddleware('throttle:api');

Route::get('/shop/brands/browse-list', [RetrieveBrandsController::class, 'getPaginated'])
    ->withoutMiddleware('throttle:api');

Route::get('/shop/brands/{brandId}', [RetrieveBrandsController::class, 'getBrandInfo'])
    ->withoutMiddleware('throttle:api');
