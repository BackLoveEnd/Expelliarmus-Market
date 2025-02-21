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
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('api.')->group(module_path($this->name, '/routes/api.php'));
    }
}
