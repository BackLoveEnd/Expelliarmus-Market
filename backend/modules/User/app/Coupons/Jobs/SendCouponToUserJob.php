<?php

namespace Modules\User\Coupons\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Order\Order\Models\Order;
use Modules\User\Coupons\Emails\CouponMail;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Users\Models\User;
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

            $existing = DB::table('coupon_user')
                ->join('coupons', 'coupons.id', '=', 'coupon_user.coupon_id')
                ->where(function ($q) use ($ordersMeta) {
                    $q
                        ->whereIn('coupon_user.user_id', $ordersMeta->pluck('userable_id')->filter()->unique())
                        ->orWhereIn('coupon_user.email', $ordersMeta->pluck('contact_email')->filter()->unique());
                })
                ->select('coupon_user.user_id', 'coupon_user.email', 'coupons.discount')
                ->get();

            $couponMeta = $this->filterExistsCouponForUser($couponMeta, $existing);

            if ($couponMeta->isEmpty()) {
                $i++;

                continue;
            }

            $now = now()->addMonth();

            $newCoupons = collect();
            $couponIdToMeta = [];

            foreach ($couponMeta as $meta) {
                $code = Str::upper(Str::random(10));

                $newCoupons->push([
                    'coupon_id' => $code,
                    'discount' => $meta->discount_amount,
                    'type' => CouponTypeEnum::PERSONAL->value,
                    'expires_at' => $now,
                ]);

                $couponIdToMeta[$code] = $meta;
                $meta->coupon_code = $code;
            }

            Coupon::query()->insert($newCoupons->toArray());

            $insertedCoupons = Coupon::query()
                ->whereIn('coupon_id', array_keys($couponIdToMeta))
                ->get(['id', 'coupon_id']);

            $couponUserLinks = [];

            foreach ($insertedCoupons as $coupon) {
                $meta = $couponIdToMeta[$coupon->coupon_id];

                $couponUserLinks[] = [
                    'coupon_id' => $coupon->id,
                    'user_id' => $meta->userable_type === User::class ? $meta->userable_id : null,
                    'email' => $meta->userable_type === User::class ? null : $meta->contact_email,
                    'usage_number' => 0,
                ];
            }

            DB::table('coupon_user')->insert($couponUserLinks);

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

                return (object) [
                    'userable_id' => $order->userable_id,
                    'userable_type' => $order->userable_type,
                    'order_count' => $order->order_count,
                    'discount_amount' => $discountAmount,
                    'contact_email' => $order->contact_email,
                ];
            })
            ->filter()
            ->values();
    }

    public function filterExistsCouponForUser(
        \Illuminate\Support\Collection $couponMeta,
        \Illuminate\Support\Collection $existing,
    ) {
        return $couponMeta->filter(function (stdClass $meta) use ($existing) {
            return ! $existing->contains(function ($row) use ($meta) {
                $matchesUser = $meta->userable_type === User::class && $row->user_id === $meta->userable_id;
                $matchesEmail = $meta->userable_type !== User::class && $row->email === $meta->contact_email;

                return ($matchesUser || $matchesEmail) && $row->discount === $meta->discount_amount;
            });
        })->values();
    }

    public function fail($exception = null): void
    {
        throw $exception;
    }

    private function sendEmailsWithCoupons(\Illuminate\Support\Collection $couponMeta): void
    {
        $couponMeta->each(function (stdClass $coupon) {
            Mail::to($coupon->contact_email)->queue(
                (new CouponMail(
                    couponCode: $coupon->coupon_code,
                    expiresAt: now()->addMonth(),
                    discount: $coupon->discount_amount,
                ))->onQueue('low'),
            );
        });
    }
}
