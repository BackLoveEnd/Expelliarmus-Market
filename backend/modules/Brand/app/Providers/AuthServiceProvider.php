<?php

declare(strict_types=1);

namespace Modules\Brand\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as LaraAuthProvider;
use Modules\Brand\Models\Brand;
use Modules\Brand\Policies\BrandPolicy;

class AuthServiceProvider extends LaraAuthProvider
{
    protected $policies = [
        Brand::class => BrandPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
