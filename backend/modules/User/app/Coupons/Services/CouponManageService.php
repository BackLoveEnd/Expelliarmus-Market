<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\User\Coupons\Dto\CouponDto;
use Modules\User\Coupons\Dto\CouponEditDto;
use Modules\User\Coupons\Dto\CouponUserDto;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Coupons\Events\CouponAssignedToUser;
use Modules\User\Coupons\Exceptions\CouponNotValidException;
use Modules\User\Coupons\Exceptions\FailedToUpdateCouponException;
use Modules\User\Coupons\Exceptions\PersonalCouponMustHaveUserException;
use Modules\User\Coupons\Exceptions\ReachedGlobalCouponUserLimitException;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Users\Models\User;
use Throwable;

class CouponManageService
{
    public function checkCoupon(string $couponCode, User|string|null $user): Coupon
    {
        $coupon = Coupon::query()
            ->where('coupon_id', $couponCode)
            ->where('expires_at', '>=', now())
            ->first();

        if (! $coupon) {
            throw new CouponNotValidException;
        }

        if ($coupon->type->is(CouponTypeEnum::GLOBAL)) {
            if ($user !== null && $this->isGlobalCouponUsageLimitReached($coupon, $user)) {
                throw new ReachedGlobalCouponUserLimitException;
            }

            return $coupon;
        }

        $query = DB::table('coupon_user')
            ->where('coupon_id', $coupon->id);

        if ($user instanceof User) {
            $query->where('user_id', $user->id);
        } elseif (is_string($user)) {
            $query->where('email', $user);
        } else {
            throw new CouponNotValidException;
        }

        $entry = $query->first();

        if (! $entry) {
            throw new CouponNotValidException;
        }

        return $coupon;
    }

    public function isGlobalCouponUsageLimitReached(Coupon $coupon, User|string $user): bool
    {
        if (! $coupon->type->is(CouponTypeEnum::GLOBAL)) {
            throw new InvalidArgumentException('Coupon must be global.');
        }

        if ($user instanceof User) {
            return $user
                ->coupons()
                ->where('usage_number', '>=', config('user.coupons.usage_limit', 3))
                ->exists();
        }

        return DB::table('coupon_user')
            ->where('email', $user)
            ->where('coupon_id', $coupon->id)
            ->where('usage_number', '>=', config('user.coupons.usage_limit', 3))
            ->exists();
    }

    public function createCoupon(CouponDto $dto): CouponUserDto|Coupon
    {
        $couponDto = DB::transaction(function () use ($dto) {
            if ($dto->type->is(CouponTypeEnum::GLOBAL)) {
                return $this->saveGlobalCoupon($dto);
            }

            if (! $dto->email && $dto->type->is(CouponTypeEnum::PERSONAL)) {
                throw new PersonalCouponMustHaveUserException;
            }

            return $this->savePersonalCoupon($dto);
        });

        if ($couponDto instanceof CouponUserDto) {
            if ($couponDto->user instanceof User) {
                event(new CouponAssignedToUser($couponDto->user->email, $couponDto->coupon));
            } else {
                event(new CouponAssignedToUser($couponDto->user, $couponDto->coupon));
            }
        }

        return $couponDto;
    }

    public function updateCoupon(Coupon $coupon, CouponEditDto $dto): void
    {
        try {
            $fieldsToUpdate = [
                'discount' => $dto->discount,
                'expires_at' => $dto->expiresAt,
            ];

            if ($coupon->type->is(CouponTypeEnum::GLOBAL)) {
                $coupon->update($fieldsToUpdate);

                return;
            }

            if (! $dto->email && $coupon->type->is(CouponTypeEnum::PERSONAL)) {
                throw new PersonalCouponMustHaveUserException;
            }

            $user = User::query()->where('email', $dto->email)->first(['id', 'email']);

            $coupon->update($fieldsToUpdate);

            $couponUser = DB::table('coupon_user')
                ->where('coupon_id', $coupon->id)
                ->first();

            if ($user) {
                $oldUserId = $couponUser->user_id ?? null;

                DB::table('coupon_user')
                    ->updateOrInsert(
                        ['coupon_id' => $coupon->id],
                        [
                            'user_id' => $user->id,
                            'email' => null,
                            'usage_number' => $oldUserId !== $user->id ? 0 : ($couponUser->usage_number ?? 0),
                        ],
                    );

                if ($oldUserId !== $user->id) {
                    event(new CouponAssignedToUser($user->email, $coupon));
                }
            } else {
                $oldEmail = $couponUser->email ?? null;

                DB::table('coupon_user')
                    ->updateOrInsert(
                        ['coupon_id' => $coupon->id],
                        [
                            'user_id' => null,
                            'email' => $dto->email,
                            'usage_number' => $oldEmail !== $dto->email ? 0 : ($couponUser->usage_number ?? 0),
                        ],
                    );

                if ($oldEmail !== $dto->email) {
                    event(new CouponAssignedToUser($dto->email, $coupon));
                }
            }
        } catch (Throwable $e) {
            throw new FailedToUpdateCouponException($e->getMessage());
        }
    }

    public function deleteCoupon(Coupon $coupon): void
    {
        $this->deleteGlobalCoupon($coupon);

        $this->deletePersonalCoupon($coupon);
    }

    public function increaseGlobalCouponUsage(Coupon $coupon, User|string $user): void
    {
        if (! $coupon->type->is(CouponTypeEnum::GLOBAL)) {
            return;
        }

        if ($user instanceof User) {
            $relation = DB::table('coupon_user')
                ->where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->first();

            if ($relation) {
                $user->coupons()->updateExistingPivot($coupon->id, [
                    'usage_number' => DB::raw('usage_number + 1'),
                ]);
            } else {
                $user->coupons()->attach($coupon->id, ['usage_number' => 1]);
            }
        } else {
            $entry = DB::table('coupon_user')
                ->where('email', $user)
                ->where('coupon_id', $coupon->id)
                ->first();

            if ($entry) {
                DB::table('coupon_user')
                    ->where('email', $user)
                    ->where('coupon_id', $coupon->id)
                    ->increment('usage_number');
            } else {
                DB::table('coupon_user')->insert([
                    'coupon_id' => $coupon->id,
                    'email' => $user,
                    'usage_number' => 1,
                    'user_id' => null,
                ]);
            }
        }
    }

    public function deletePersonalCoupon(Coupon $coupon): void
    {
        if ($coupon->type->is(CouponTypeEnum::PERSONAL)) {
            $coupon->delete();
        }
    }

    public function deleteGlobalCoupon(Coupon $coupon): void
    {
        if ($coupon->type->is(CouponTypeEnum::GLOBAL)) {
            $coupon->delete();
        }
    }

    protected function saveGlobalCoupon(CouponDto $dto): Coupon
    {
        return Coupon::query()->create([
            'coupon_id' => $dto->couponCode ?? randomString(12, true),
            'discount' => $dto->discount,
            'type' => $dto->type->value,
            'expires_at' => $dto->expiresAt,
        ]);
    }

    protected function savePersonalCoupon(CouponDto $dto): CouponUserDto
    {
        $user = User::query()->where('email', $dto->email)->first(['id', 'email']);

        $coupon = Coupon::query()->create([
            'coupon_id' => $dto->couponCode ?? randomString(12, true),
            'discount' => $dto->discount,
            'type' => $dto->type->value,
            'expires_at' => $dto->expiresAt,
        ]);

        if ($user) {
            $coupon->users()->attach($user->id, [
                'email' => null,
                'usage_number' => 0,
            ]);

            return new CouponUserDto(
                coupon: $coupon,
                user: $user,
            );
        }

        $coupon->users()->create([
            'email' => $dto->email,
            'user_id' => null,
            'usage_number' => 0,
        ]);

        return new CouponUserDto(
            coupon: $coupon,
            user: $dto->email,
        );
    }
}
