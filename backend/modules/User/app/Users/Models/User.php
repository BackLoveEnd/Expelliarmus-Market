<?php

namespace Modules\User\Users\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Order\Cart\Models\Cart;
use Modules\Order\Order\Models\Order;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Users\Contracts\UserInterface;
use Modules\User\Users\Observers\UserObserver;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;
use Propaganistas\LaravelPhone\Rules\Phone;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $user_id
 * @property Carbon $email_verified_at
 * @property string $password
 * @property string $address
 * @property Carbon $created_at
 * @property string $phone_country_code
 * @property Phone $phone_number
 * @property int $id
 */
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements UserInterface
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'address',
        'phone_country_code',
        'phone_number',
    ];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'address',
        'phone_country_code',
        'phone_number',
    ];

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'userable');
    }

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class)->withPivot('email', 'usage_number');
    }

    public function wishlist(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'phone_number' => E164PhoneNumberCast::class.':UA,US',
        ];
    }

    public function userUuid(): string
    {
        return $this->user_id;
    }

    public function fullName(): string
    {
        return $this->last_name.' '.$this->first_name;
    }

    protected static function newFactory(): UserFactory
    {
        return new UserFactory;
    }
}
