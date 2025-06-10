<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Product\Database\Factories\ProductSpecFactory;

/**
 * @property int $id
 * @property string $spec_name
 * @property string $group
 */
class ProductSpecAttributes extends Model
{
    use HasFactory;

    protected $table = 'product_specs_attributes';

    public $timestamps = false;

    protected $fillable = [
        'spec_name',
        'category_id',
        'group_name',
    ];

    public function product(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_spec',
            'attribute_id',
            'product_id'
        )->using(ProductSpec::class);
    }

    protected static function newFactory(): ProductSpecFactory
    {
        return ProductSpecFactory::new();
    }
}
