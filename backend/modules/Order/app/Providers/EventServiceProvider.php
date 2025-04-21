<?php

namespace Modules\Order\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Order\Order\Events\OrderCreated;
use Modules\Order\Order\Listeners\SendNotificationAboutOrderToUser;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderCreated::class => [
            SendNotificationAboutOrderToUser::class,
        ],
    ];

    protected static $shouldDiscoverEvents = true;

    protected function configureEmailVerification(): void
    {
        //
    }
}
