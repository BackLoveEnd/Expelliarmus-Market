<?php

namespace Modules\Product\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Order\Order\Models\OrderLine;
use Modules\Product\Builders\ProductBuilder;
use Modules\Product\Casts\ProductArticleCast;
use Modules\Product\Database\Factories\ProductFactory;
use Modules\Product\Observers\ProductObserver;
use Modules\Product\Traits\Slugger;
use Modules\Warehouse\Contracts\DiscountRelationInterface;
use Modules\Warehouse\Contracts\VariationInterface;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Models\Discount;
use Modules\Warehouse\Models\ProductAttributeValue;
use Modules\Warehouse\Models\ProductVariation;
use Modules\Warehouse\Models\Warehouse;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property int $category_id
 * @property int $brand_id
 * @property string $product_article
 * @property string $title_description
 * @property string $main_description_markdown
 * @property string $main_description_html
 * @property array $images
 * @property string $preview_image
 * @property string $preview_image_source
 * @property Carbon $created_at
 * @property Carbon $published_at
 * @property Warehouse $warehouse
 * @property ProductStatusEnum $status
 * @property bool|null $with_attribute_combinations
 *
 * @method ProductBuilder wherePublished
 * @method ProductBuilder whereNotPublished
 */
#[ObservedBy(ProductObserver::class)]
class Product extends Model implements DiscountRelationInterface
{
    use HasFactory;
    use Slugger;
    use SoftDeletes;

    public const int WITHOUT_OPTIONS = 0;

    public const int SINGLE_OPTION = 1;

    public const int COMBINED_OPTION = 3;

    protected $fillable = [
        'title',
        'category_id',
        'brand_id',
        'title_description',
        'product_article',
        'main_description_markdown',
        'main_description_html',
        'images',
        'preview_image',
        'preview_image_source',
        'with_attribute_combinations',
    ];

    public function orderLines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouse(): HasOne
    {
        return $this->hasOne(Warehouse::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function productSpecs(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                ProductSpecAttributes::class,
                'product_specs',
                'product_id',
                'attribute_id',
            )->withPivot(['value'])
            ->using(ProductSpec::class);
    }

    public function singleAttributes(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function combinedAttributes(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    // For products that do not have options defining their prices.
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

    public function getDescriptionMarkdown(): string
    {
        return $this->main_description_markdown;
    }

    public function getDescriptionHtml(): string
    {
        return $this->main_description_html;
    }

    public function getCurrentVariationRelation(): Collection|VariationInterface|null
    {
        if (is_null($this->hasCombinedAttributes())) {
            return null;
        }

        return $this->hasCombinedAttributes()
            ? $this->combinedAttributes
            : $this->singleAttributes;
    }

    public function productRootCategory(): int
    {
        return once(function () {
            return Category::defaultOrder()->ancestorsOf($this->category_id)->first()->id;
        });
    }

    public function hasCombinedAttributes(bool $original = false): ?bool
    {
        if ($original) {
            return $this->getOriginal('with_attribute_combinations');
        }

        return $this->with_attribute_combinations;
    }

    public function changeStatus(ProductStatusEnum $status): void
    {
        if ($this->status === $status) {
            return;
        }

        $this->status = $status->value;

        $this->save();
    }

    public function moveToTrash(): void
    {
        $this->changeStatus(ProductStatusEnum::TRASHED);

        $this->delete();

        $this->warehouse()->delete();
    }

    public function publish(): void
    {
        if ($this->status === ProductStatusEnum::NOT_PUBLISHED) {
            $this->status = ProductStatusEnum::PUBLISHED;

            $this->published_at = now();

            $this->save();
        }
    }

    public function unPublish(): void
    {
        if ($this->status === ProductStatusEnum::PUBLISHED) {
            $this->status = ProductStatusEnum::NOT_PUBLISHED->value;

            $this->published_at = null;

            $this->save();
        }
    }

    public function saveImages(array $images): void
    {
        if (array_key_exists('images', $images)) {
            $this->saveMainImages($images['images']);
        }

        if (array_key_exists('preview_image', $images) && array_key_exists('preview_image_source', $images)) {
            $this->savePreviewImage($images['preview_image'], $images['preview_image_source']);
        }

        $this->save();
    }

    public function savePreviewImage(?string $url, ?string $source = null): void
    {
        $this->preview_image = $url;

        $this->preview_image_source = $source;

        $this->save();
    }

    public function saveMainImages(?array $imagesUrls): void
    {
        $this->images = $imagesUrls;

        $this->save();
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'published_at' => 'datetime',
            'images' => 'array',
            'with_attribute_combinations' => 'boolean',
            'product_article' => ProductArticleCast::class,
            'status' => ProductStatusEnum::class,
        ];
    }

    protected function slugColumn(): string
    {
        return 'slug';
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Product $product) {
            if ($product->created_at === null) {
                $product->created_at = Carbon::now();
            }

            if (str_starts_with($product->product_article, '#')) {
                $product->product_article .= '-'.Str::random(4);
            } else {
                $product->product_article = '#'.$product->product_article.'-'.Str::random(4);
            }

            $product->slug = $product->createSlug($product->title);
        });

        static::updating(function (Product $product) {
            $product->slug = $product->createSlug($product->title);
        });
    }

    public function newEloquentBuilder($query): ProductBuilder
    {
        return new ProductBuilder($query);
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
