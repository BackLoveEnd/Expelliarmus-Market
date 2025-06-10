<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\User\Coupons\Emails\CouponMail;
use Modules\User\Coupons\Events\CouponAssignedToUser;

class SendCouponEmailListener implements ShouldQueue
{
    public $queue = 'high';

    public function __construct() {}

    public function handle(CouponAssignedToUser $event): void
    {
        Mail::to($event->email)->send(
            (new CouponMail(
                couponCode: $event->coupon->coupon_id,
                expiresAt: $event->coupon->expires_at,
                discount: $event->coupon->discount,
            ))->onQueue('low'),
        );
    }
}
