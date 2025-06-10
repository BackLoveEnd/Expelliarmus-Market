<?php

declare(strict_types=1);

namespace Modules\Product\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as LaraAuthProvider;
use Modules\Product\Http\Management\Policies\ProductPolicy;
use Modules\Product\Models\Product;

class AuthServiceProvider extends LaraAuthProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
