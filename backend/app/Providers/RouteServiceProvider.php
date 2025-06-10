<?php

declare(strict_types=1);

namespace App\Providers;

use App\Helpers\RequestModel;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as LaraRouteService;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends LaraRouteService
{
    public function boot(): void
    {
        parent::boot();

        Route::bind('model', function (mixed $id, \Illuminate\Routing\Route $route) {
            return new RequestModel(
                id: $id,
                bindingField: $route->bindingFields() ? array_values($route->bindingFields())[0] : 'id',
            );
        });
    }
}
