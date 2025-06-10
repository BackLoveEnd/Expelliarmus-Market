<?php

namespace Modules\Warehouse\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Product\Models\Product;
use Modules\Product\Traits\Slugger;
use Modules\Warehouse\Contracts\DiscountRelationInterface;
use Modules\Warehouse\Contracts\VariationInterface;
use Modules\Warehouse\Database\Factories\ProductVariationFactory;

/**
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property float $price,
 * @property string $sku
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductVariation extends Model implements DiscountRelationInterface, VariationInterface
{
    use HasFactory;
    use Slugger;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'quantity',
        'price',
        'sku',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function price(): Attribute
    {
        return Attribute::get(function ($value) {
            return round((float) $value, 2);
        });
    }

    public function productAttributes(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                ProductAttribute::class,
                'variation_attribute_values',
                'variation_id',
                'attribute_id',
            )
            ->withPivot(['id', 'value']);
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

    public function lastActiveDiscount(): MorphMany
    {
        return $this
            ->discount()
            ->active()
            ->orderByDesc('discounts.end_date');
    }

    public function variationsAttributesValues(): HasMany
    {
        return $this->hasMany(VariationAttributeValues::class, 'variation_id');
    }

    public function quantity(): Attribute
    {
        return Attribute::set(fn ($value) => max($value, 0));
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ProductVariation $variation) {
            $variation->sku = $variation->createSlug($variation->sku);
        });

        static::updating(function (ProductVariation $variation) {
            $variation->sku = $variation->createSlug($variation->sku);
        });
    }

    protected function slugColumn(): string
    {
        return 'sku';
    }

    protected static function newFactory(): ProductVariationFactory
    {
        return ProductVariationFactory::new();
    }
}
