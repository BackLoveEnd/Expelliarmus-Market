<?php

namespace Modules\Brand\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;
use Modules\Brand\Database\Factories\BrandFactory;
use Modules\Brand\Observers\BrandObserver;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $logo_url
 * @property string $logo_source
 */
#[ObservedBy(BrandObserver::class)]
class Brand extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'logo_source',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function categories(): HasManyThrough
    {
        return $this->hasManyThrough(
            Category::class,
            Product::class,
            'brand_id',
            'id',
            'id',
            'category_id',
        )->distinct();
    }

    public function hasProducts(): bool
    {
        if ($this->relationLoaded('products')) {
            return $this->products->count() > 0;
        }

        return $this->products()->count() > 0;
    }

    public function saveLogo(string $url, string $source): void
    {
        $this->logo_url = $url;

        $this->logo_source = $source;

        $this->save();
    }

    protected static function newFactory(): BrandFactory
    {
        return new BrandFactory;
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
