<?php

namespace Modules\User\Users\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Order\Cart\Models\Cart;
use Modules\Order\Order\Models\Order;
use Modules\User\Database\Factories\GuestFactory;
use Modules\User\Users\Contracts\UserInterface;
use Modules\User\Users\Observers\GuestObserver;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;
use Propaganistas\LaravelPhone\Rules\Phone;

/**
 * @property int $id,
 * @property string $guest_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $address
 * @property string $phone_country_code
 * @property Phone $phone_number
 * @property Carbon $created_at
 */
#[ObservedBy(GuestObserver::class)]
class Guest extends Model implements UserInterface
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_country_code',
        'address',
        'phone_number',
    ];

    protected $hidden = [
        'phone_country_code',
        'phone_number',
    ];

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'userable');
    }

    public function cart(): MorphOne
    {
        return $this->morphOne(Cart::class, 'userable');
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'phone_number' => E164PhoneNumberCast::class.':UA,US',
        ];
    }

    public function fullName(): string
    {
        return $this->last_name.' '.$this->first_name;
    }

    protected static function newFactory(): GuestFactory
    {
        return new GuestFactory;
    }
}
