<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Category\Models\Category;
use Modules\Warehouse\Database\Factories\ProductAttributeFactory;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;
use Modules\Warehouse\Enums\ProductAttributeViewTypeEnum;
use Modules\Warehouse\Observers\ProductAttributeObserver;

/**
 * @property string $name
 * @property ProductAttributeTypeEnum $type
 * @property ProductAttributeViewTypeEnum $view_type
 * @property bool $required
 */
#[ObservedBy(ProductAttributeObserver::class)]
class ProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'product_attributes';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'required',
        'view_type',
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductAttributeTypeEnum::class,
            'view_type' => ProductAttributeViewTypeEnum::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function singleAttributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }

    public function productVariations(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                ProductVariation::class,
                'variation_attribute_values',
                'attribute_id',
                'variation_id',
            )
            ->withPivot('value');
    }

    public function combinedPivotAttributesValues(): HasMany
    {
        return $this->hasMany(VariationAttributeValues::class, 'attribute_id');
    }

    public function hasUsageInProductsWithSingleAttributes(): bool
    {
        if ($this->relationLoaded('singleAttributeValues')) {
            return $this->singleAttributeValues->count() > 0;
        }

        return $this->singleAttributeValues()->count() > 0;
    }

    public function hasUsageInProductsWithCombinedAttributes(): bool
    {
        if ($this->relationLoaded('productVariations')) {
            return $this->productVariations->count() > 0;
        }

        return $this->productVariations()->count() > 0;
    }

    protected static function newFactory(): ProductAttributeFactory
    {
        return ProductAttributeFactory::new();
    }
}
