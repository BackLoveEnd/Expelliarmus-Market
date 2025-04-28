<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Order\Database\Factories\CouponFactory;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Users\Models\User;

/**
 * @property string $coupon_id
 * @property int $discount
 * @property CouponTypeEnum $type
 * @property int $user_id
 * @property string $email
 * @property Carbon $expires_at
 */
class Coupon extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'coupon_id',
        'discount',
        'user_id',
        'email',
        'type',
        'expires_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'type' => CouponTypeEnum::class,
        ];
    }

    protected static function newFactory(): CouponFactory
    {
        return CouponFactory::new();
    }
}