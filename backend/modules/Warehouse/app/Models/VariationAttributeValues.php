<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $variation_id
 * @property int $attribute_id
 * @property string $value
 */
class VariationAttributeValues extends Pivot
{
    use HasFactory;

    protected $table = 'variation_attribute_values';

    public $timestamps = false;

    protected $fillable = [
        'variation_id',
        'attribute_id',
        'value',
    ];

    public function value(): Attribute
    {
        return Attribute::get(function ($value) {
            return $this->attribute->type->castToType($value);
        });
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }
}
