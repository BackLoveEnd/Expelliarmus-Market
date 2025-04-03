<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Warehouse\Models\Product;

/**
 * @property int $product_id
 * @property int $order_id
 * @property int $quantity
 * @property float $total_price
 * @property float $price_per_unit_at_order_time
 */
class OrderLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'total_price',
        'price_per_unit_at_order_time'
    ];

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
}
