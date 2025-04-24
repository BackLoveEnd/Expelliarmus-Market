<?php

declare(strict_types=1);

namespace Modules\Order\Order\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CouponMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private string $couponCode,
        private string $expiresAt,
        private string $discount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Get your coupon',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mails.coupons.present',
        );
    }
}