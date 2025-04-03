<?php

namespace Modules\User\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'User';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapShopRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('api.')->group(module_path($this->name, '/routes/api.php'));
    }

    protected function mapShopRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('shop.')->group(module_path($this->name, '/routes/shop.php'));
    }
}
