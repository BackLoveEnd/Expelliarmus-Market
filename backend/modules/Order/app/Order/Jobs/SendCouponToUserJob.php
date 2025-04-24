<?php

namespace Modules\Order\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Order\Order\Emails\CouponMail;
use Modules\Order\Order\Enum\CouponTypeEnum;
use Modules\Order\Order\Models\Coupon;
use Modules\Order\Order\Models\Order;
use Modules\User\Models\User;
use stdClass;

class SendCouponToUserJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $i = 0;

        $perPage = 100;

        do {
            $ordersMeta = Order::query()
                ->selectRaw('userable_id, userable_type, contact_email, COUNT(*) as order_count')
                ->groupBy('userable_id', 'userable_type', 'contact_email')
                ->orderBy('userable_id')
                ->skip($i * $perPage)
                ->take($perPage)
                ->get();

            $couponMeta = $this->getCouponMeta($ordersMeta);

            $coupons = Coupon::query()
                ->whereIn('user_id', $ordersMeta->pluck('userable_id'))
                ->orWhereIn('email', $ordersMeta->pluck('contact_email'))
                ->where('type', CouponTypeEnum::PERSONAL)
                ->get(['user_id', 'email', 'coupon_id', 'discount']);

            $couponMeta = $this->filterExistsCouponForUser($couponMeta, $coupons);

            $couponsToDbFields = $couponMeta->map(function (stdClass $meta) {
                return [
                    'user_id' => $meta->userable_type === User::class ? $meta->userable_id : null,
                    'email' => $meta->userable_type === User::class ? null : $meta->contact_email,
                    'coupon_id' => $meta->coupon_code,
                    'discount' => $meta->discount_amount,
                    'type' => CouponTypeEnum::PERSONAL->value,
                    'expires_at' => now()->addMonth(),
                ];
            });

            Coupon::query()->insert($couponsToDbFields->toArray());

            $this->sendEmailsWithCoupons($couponMeta);

            $i++;
        } while ($ordersMeta->count() > 0 && $i < 100);
    }

    public function getCouponMeta(Collection $ordersMeta): \Illuminate\Support\Collection
    {
        return $ordersMeta
            ->map(function (Order $order) {
                $discountAmount = match (true) {
                    $order->order_count >= 3 && $order->order_count <= 5 => 10,
                    $order->order_count > 5 && $order->order_count <= 10 => 15,
                    $order->order_count > 10 && $order->order_count <= 20 => 18,
                    $order->order_count > 30 => 25,
                    default => 0,
                };

                if ($discountAmount === 0) {
                    return null;
                }

                $couponCode = Str::upper(Str::random(10));

                return (object)[
                    'userable_id' => $order->userable_id,
                    'userable_type' => $order->userable_type,
                    'order_count' => $order->order_count,
                    'coupon_code' => $couponCode,
                    'discount_amount' => $discountAmount,
                    'contact_email' => $order->contact_email,
                ];
            })
            ->filter()
            ->values();
    }

    public function filterExistsCouponForUser(\Illuminate\Support\Collection $couponMeta, Collection $coupons)
    {
        return $couponMeta->filter(function (stdClass $meta) use ($coupons) {
            return ! $coupons->contains(function (Coupon $coupon) use ($meta) {
                return (
                    (($meta->userable_type === User::class && $coupon->user_id === $meta->userable_id)
                        || $coupon->email === $meta->contact_email)
                    &&
                    $coupon->discount === $meta->discount_amount
                );
            });
        })->values();
    }

    private function sendEmailsWithCoupons(\Illuminate\Support\Collection $couponMeta): void
    {
        $couponMeta->each(function (stdClass $coupon) {
            Mail::to($coupon->contact_email)->queue(
                new CouponMail(
                    couponCode: $coupon->coupon_code,
                    expiresAt: now()->addMonth(),
                    discount: $coupon->discount_amount,
                ),
            );
        });
    }
}
