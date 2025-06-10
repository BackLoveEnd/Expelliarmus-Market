<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as LaraAuthProvider;
use Modules\ContentManagement\Models\ContentSlider;
use Modules\ContentManagement\Models\NewArrival;
use Modules\ContentManagement\Policies\ContentPolicy;

class AuthServiceProvider extends LaraAuthProvider
{
    protected $policies = [
        ContentSlider::class => ContentPolicy::class,
        NewArrival::class => ContentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
