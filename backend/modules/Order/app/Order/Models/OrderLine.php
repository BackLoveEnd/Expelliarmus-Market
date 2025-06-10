<?php

namespace Modules\Order\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Order\Database\Factories\OrderLineFactory;
use Modules\Product\Models\Product;

/**
 * @property int $product_id
 * @property int $order_id
 * @property int $quantity
 * @property float $total_price
 * @property array|null $variation
 * @property float $price_per_unit_at_order_time
 */
class OrderLine extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'total_price',
        'price_per_unit_at_order_time',
    ];

    protected function casts(): array
    {
        return [
            'variation' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function priceAtOrderTime(): float
    {
        return $this->price_per_unit_at_order_time;
    }

    protected static function newFactory(): OrderLineFactory
    {
        return OrderLineFactory::new();
    }
}
