<?php

namespace Modules\Warehouse\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\Models\Product;
use Modules\Warehouse\Builders\WarehouseBuilder;
use Modules\Warehouse\Database\Factories\WarehouseFactory;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;

/**
 * @property int $id
 * @property int $product_id
 * @property int $total_quantity
 * @property float $default_price
 * @property WarehouseProductStatusEnum $status
 * @property Carbon $arrived_at
 * @property Carbon $published_at
 */
class Warehouse extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'total_quantity',
        'default_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'arrived_at' => 'datetime',
            'published_at' => 'datetime',
            'status' => WarehouseProductStatusEnum::class,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function defaultPrice(): Attribute
    {
        return Attribute::get(function ($value) {
            return round((float) $value, 2);
        });
    }

    public function totalQuantity(): Attribute
    {
        return Attribute::set(fn ($value) => max($value, 0));
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Warehouse $warehouse) {
            if (! $warehouse->arrived_at) {
                $warehouse->arrived_at = Carbon::now();
            }
        });
    }

    public function newEloquentBuilder($query): WarehouseBuilder
    {
        return new WarehouseBuilder($query);
    }

    protected static function newFactory(): WarehouseFactory
    {
        return WarehouseFactory::new();
    }
}
