<?php

namespace Modules\Order\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Order';

    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapManagementRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('shop.')
            ->group(module_path($this->name, '/routes/shop.php'));
    }

    protected function mapManagementRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('management.')
            ->group(module_path($this->name, '/routes/management.php'));
    }
}
