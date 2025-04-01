<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property int $quantity
 * @property float $price_per_unit
 * @property float $final_price
 * @property array $variation
 * @property array $discount
 * @property int $product_id
 */
class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    public $incrementing = false;

    public $timestamps = false;

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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
            if ($cart->id === null) {
                $cart->id = Uuid::uuid7()->toString();
            }
        });
    }
}
