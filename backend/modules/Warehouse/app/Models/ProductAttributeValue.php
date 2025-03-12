<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Product\Models\Product;
use Modules\Warehouse\Contracts\DiscountRelationInterface;
use Modules\Warehouse\Database\Factories\SingleAttributeFactory;

class ProductAttributeValue extends Model implements DiscountRelationInterface
{
    use HasFactory;

    protected $table = 'product_attribute_values';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'attribute_id',
        'quantity',
        'price',
        'value',
    ];

    public function value(): Attribute
    {
        return Attribute::get(function ($value) {
            return $this->attribute->type->castToType($value);
        });
    }

    public function price(): Attribute
    {
        return Attribute::get(function ($value) {
            return round((float) $value, 2);
        });
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function discount(): MorphMany
    {
        return $this->morphMany(Discount::class, 'discountable');
    }

    public function lastDiscount(): MorphMany
    {
        return $this
            ->discount()
            ->notCancelled()
            ->whereDate('discounts.end_date', '>', now()->format('Y-m-d H:i:s'))
            ->orderByDesc('discounts.end_date');
    }

    protected static function newFactory(): SingleAttributeFactory
    {
        return SingleAttributeFactory::new();
    }
}