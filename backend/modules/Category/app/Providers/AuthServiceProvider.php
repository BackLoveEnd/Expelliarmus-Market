<?php

declare(strict_types=1);

namespace Modules\Category\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as LaraAuthProvider;
use Modules\Category\Models\Category;
use Modules\Category\Policies\CategoryPolicy;

class AuthServiceProvider extends LaraAuthProvider
{
    protected $policies = [
        Category::class => CategoryPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
