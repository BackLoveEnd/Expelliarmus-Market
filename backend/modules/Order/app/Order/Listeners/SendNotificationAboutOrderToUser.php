<?php

namespace Modules\Order\Order\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Order\Emails\OrderCreatedMail;
use Modules\Order\Order\Events\OrderCreated;

class SendNotificationAboutOrderToUser implements ShouldQueue
{
    public $queue = 'high';

    public function __construct()
    {
        //
    }

    public function handle(OrderCreated $event): void
    {
        Mail::to($event->user->email)->send(
            new OrderCreatedMail(
                orderLines: $event->orderLines,
                user: $event->user,
            ),
        );
    }
}
