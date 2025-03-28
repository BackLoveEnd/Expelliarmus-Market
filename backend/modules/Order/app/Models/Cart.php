<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;
use Ramsey\Uuid\Uuid;

/**
 * @property string $cart_id
 * @property int $product_id
 */
class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    public $incrementing = false;

    protected $fillable = [
        'product_id',
        'quantity',
        'price_per_unit',
        'final_price',
        'discount',
        'variation',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'discount' => 'array',
            'variation' => 'array',
        ];
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
