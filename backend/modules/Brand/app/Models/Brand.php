<?php

namespace Modules\Brand\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\Brand\Database\Factories\BrandFactory;
use Modules\Brand\Observers\BrandObserver;
use Modules\Product\Models\Product;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $logo
 */
#[ObservedBy(BrandObserver::class)]
class Brand extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'logo'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function hasProducts(): bool
    {
        if ($this->relationLoaded('products')) {
            return $this->products->count() > 0;
        }

        return $this->products()->count() > 0;
    }

    protected static function newFactory(): BrandFactory
    {
        return new BrandFactory();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Brand $brand) {
            $brand->slug = Str::slug($brand->name);
        });

        static::updating(function (Brand $brand) {
            $brand->slug = Str::slug($brand->name);
        });
    }
}
