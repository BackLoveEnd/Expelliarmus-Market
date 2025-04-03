<?php

namespace Modules\User\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Order\Models\Cart;
use Modules\Order\Models\Order;
use Modules\User\Contracts\UserInterface;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Observers\UserObserver;
use Propaganistas\LaravelPhone\Rules\Phone;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $user_id
 * @property Carbon $email_verified_at
 * @property string $password
 * @property Carbon $created_at
 * @property string $phone_country_code
 * @property Phone $phone_number
 * @property int $id
 */
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements UserInterface
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_country_code',
        'phone_number',
    ];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
        return new UserFactory();
    }
}