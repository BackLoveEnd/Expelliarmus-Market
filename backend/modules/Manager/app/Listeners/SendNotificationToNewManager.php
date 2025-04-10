<?php

namespace Modules\Manager\Listeners;

use Modules\Manager\Events\ManagerCreated;
use Modules\Manager\Notifications\ManagerCreatedNotification;

class SendNotificationToNewManager
{
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ManagerCreated $event): void
    {
        $event->manager->notify(new ManagerCreatedNotification($event->tmpPassword));
    }
}
