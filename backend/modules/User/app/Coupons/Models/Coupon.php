<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Database\Factories\CouponFactory;
use Modules\User\Users\Models\User;

/**
 * @property string $coupon_id
 * @property int $discount
 * @property CouponTypeEnum $type
 * @property Carbon $expires_at
 */
class Coupon extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'coupon_id',
        'discount',
        'type',
        'expires_at',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('email', 'usage_number');
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
