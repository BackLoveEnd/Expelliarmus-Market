<?php

declare(strict_types=1);

namespace Modules\Manager\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as LaraAuthProvider;
use Modules\Manager\Models\Manager;
use Modules\Manager\Policies\ManagerPolicy;

class AuthServiceProvider extends LaraAuthProvider
{
    protected $policies = [
        Manager::class => ManagerPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
