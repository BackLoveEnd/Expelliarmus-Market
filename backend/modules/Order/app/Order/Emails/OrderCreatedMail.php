<?php

namespace Modules\Order\Order\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Modules\Order\Order\Dto\OrderLineDto;
use Modules\Order\Order\Dto\OrderLinesDto;
use Modules\User\Users\Contracts\UserInterface;

class OrderCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        /**
         * @param  Collection<OrderLineDto>  $orderLines
         */
        private OrderLinesDto $orderLines,
        private UserInterface $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your order has been created',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.orders.created',
            with: [
                'user' => $this->user,
                'orderLines' => $this->orderLines->orderLines,
                'totalPrice' => $this->orderLines->totalPrice,
                'coupon' => $this->orderLines->coupon,
            ],
        );
    }
}
