<?php

declare(strict_types=1);

namespace Modules\Warehouse\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as LaraAuthProvider;
use Modules\Warehouse\Models\Warehouse;
use Modules\Warehouse\Policies\WarehousePolicy;

class AuthServiceProvider extends LaraAuthProvider
{
    protected $policies = [
        Warehouse::class => WarehousePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
