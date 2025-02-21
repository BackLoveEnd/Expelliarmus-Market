<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Ramsey\Uuid\Uuid;

/**
 * @property string $cart_id
 * @property int $product_id
 */
class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id'
    ];

    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Cart $cart) {
            if ($cart->cart_id === null) {
                $cart->cart_id = Uuid::uuid7()->toString();
            }
        });
    }
}
