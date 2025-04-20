<?php

namespace Modules\Order\Order\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Modules\Order\Order\Dto\OrderLineDto;
use Modules\User\Contracts\UserInterface;

class OrderCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        /**
         * @param  Collection<OrderLineDto>  $orderLines
         */
        private Collection $orderLines,
        private UserInterface $user,
    ) {}

    public function content(): Content
    {
        return new Content(
            view: 'mail.orders.created',
        );
    }
}
