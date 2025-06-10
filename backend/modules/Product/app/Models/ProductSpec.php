<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $product_id
 * @property int $attribute_id
 * @property string $value
 */
class ProductSpec extends Pivot
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'product_specs';

    protected $fillable = [
        'product_id',
        'attribute_id',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }
}
