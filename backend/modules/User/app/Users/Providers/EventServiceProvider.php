<?php

namespace Modules\User\Users\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\User\Users\Events\GuestRegistered;
use Modules\User\Users\Events\UserLogin;
use Modules\User\Users\Listeners\MigrateGuestDataToRegularUserListener;
use Modules\User\Users\Listeners\SyncUserCartAfterLoginListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserLogin::class => [
            SyncUserCartAfterLoginListener::class,
        ],
        GuestRegistered::class => [
            MigrateGuestDataToRegularUserListener::class,
        ],
    ];
}
