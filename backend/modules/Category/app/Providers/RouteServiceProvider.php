<?php

namespace Modules\Category\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Category';

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
