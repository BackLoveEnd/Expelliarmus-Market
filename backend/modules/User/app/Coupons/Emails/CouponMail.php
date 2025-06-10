<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class CouponMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private string $couponCode,
        private Carbon $expiresAt,
        private int $discount,
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
            with: [
                'couponCode' => $this->couponCode,
                'discount' => $this->discount,
                'expiresAt' => $this->expiresAt->toFormattedDateString(),
            ],
        );
    }
}
