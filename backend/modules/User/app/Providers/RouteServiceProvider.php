<?php

namespace Modules\User\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'User';

    public function map(): void
    {
        $this->mapManagementRoutes();

        $this->mapShopRoutes();
    }

    protected function mapManagementRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('management.')->group(
            module_path($this->name, '/routes/management.php'),
        );
    }

    protected function mapShopRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('shop.')->group(
            module_path(
                $this->name,
                '/routes/shop.php',
            ),
        );
    }
}
