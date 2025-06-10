<?php

namespace Modules\Category\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\Collection;
use Kalnoy\Nestedset\NodeTrait;
use Modules\Category\Observers\CategoryObserver;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSpecAttributes;
use Modules\Warehouse\Models\ProductAttribute;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $icon_url
 * @property string $icon_origin
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[ObservedBy(CategoryObserver::class)]
class Category extends Model
{
    use HasFactory;
    use NodeTrait;

    protected $fillable = [
        'name',
        'icon_url',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class, 'category_id');
    }

    public function productSpecifications(): HasMany
    {
        return $this->hasMany(ProductSpecAttributes::class);
    }

    public function allAttributesFromTree(): \Illuminate\Database\Eloquent\Collection
    {
        return once(function () {
            $categories = $this->ancestorsAndSelf($this->id)->pluck('id');

            return ProductAttribute::whereIn('category_id', $categories)->get();
        });
    }

    public static function getAllCategoriesInTree(int $limit = 0): Collection
    {
        if ($limit !== 0) {
            return self::defaultOrder()->withDepth()->get()->toTree()->take($limit);
        }

        return self::defaultOrder()->withDepth()->get()->toTree();
    }

    public static function onlyRoot(): Collection
    {
        return self::whereIsRoot()->get();
    }

    public function hasProducts(): bool
    {
        if ($this->checkProducts()) {
            return true;
        }

        $children = $this
            ->descendants()->with('products')
            ->get();

        foreach ($children as $child) {
            if ($child->checkProducts()) {
                return true;
            }
        }

        return false;
    }

    public function saveFileId(string $url, string $origin): void
    {
        $this->icon_origin = $origin;

        $this->icon_url = $url;

        $this->save();
    }

    protected function checkProducts(): bool
    {
        if ($this->relationLoaded('products')) {
            return $this->products->count() > 0;
        }

        return $this->products()->count() > 0;
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Category $category) {
            $category->slug = Str::slug($category->name);
        });
    }
}
