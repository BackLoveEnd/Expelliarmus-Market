<?php

namespace Modules\Warehouse\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Number;
use Modules\Product\Models\Product;

/**
 * @property int $id
 * @property string|int $percentage
 * @property float $original_price
 * @property float $discount_price
 * @property Carbon $start_date
 * @property Carbon $end_date
 */
class Discount extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'percentage',
        'original_price',
        'discount_price',
        'start_date',
        'end_date',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function singleOptionProductDiscount(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ProductAttributeValue::class,
            table: 'product_single_variation_discounts',
            foreignPivotKey: 'discount_id',
            relatedPivotKey: 's_variation_id',
        );
    }

    public function combinedOptionsProductDiscount(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ProductVariation::class,
            table: 'product_combined_variation_discounts',
            foreignPivotKey: 'discount_id',
            relatedPivotKey: 'c_variation_id',
        );
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function percentage(): Attribute
    {
        return Attribute::get(fn($value) => Number::percentage($value));
    }

    public function originalPrice(): Attribute
    {
        return Attribute::get(function ($value) {
            return round((float) $value, 2);
        });
    }

    public function discountPrice(): Attribute
    {
        return Attribute::get(function ($value) {
            return round((float) $value, 2);
        });
    }
}
