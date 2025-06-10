<?php

declare(strict_types=1);

namespace Modules\User\Users\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Order\Order\Services\GuestMigrateOrdersService;
use Modules\User\Users\Events\GuestRegistered;
use Throwable;

class MigrateGuestDataToRegularUserListener implements ShouldQueue
{
    public $queue = 'high';

    public function __construct(
        private GuestMigrateOrdersService $guestMigrateOrdersService,
    ) {}

    public function handle(GuestRegistered $event): void
    {
        try {
            $this->guestMigrateOrdersService->process($event->guest, $event->createdUser);

            // Last operation is to delete the guest
            $event->guest->delete();
        } catch (Throwable $e) {
            $event->createdUser->delete();
        }
    }
}
