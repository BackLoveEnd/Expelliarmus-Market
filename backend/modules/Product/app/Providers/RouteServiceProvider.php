<?php

namespace Modules\Product\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Management\Support\ProductSlug;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Product';

    public function boot(): void
    {
        parent::boot();

        Route::bind('productBind', function (int $product, $route) {
            return new ProductSlug($product, $route->parameter('slug'));
        });
    }

    public function map(): void
    {
        $this->mapManagementRoute();
        $this->mapShopRoute();
    }

    protected function mapManagementRoute(): void
    {
        Route::middleware('api')->prefix('api')->name('management.')->group(
            module_path($this->name, '/routes/management.php'),
        );
    }

    protected function mapShopRoute(): void
    {
        Route::middleware('api')->prefix('api')->name('shop.')->group(
            module_path($this->name, '/routes/shop.php'),
        );
    }
}
