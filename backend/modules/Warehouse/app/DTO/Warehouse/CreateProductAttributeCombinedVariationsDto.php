<?php

declare(strict_types=1);

namespace Modules\Warehouse\DTO\Warehouse;

use App\Services\Validators\JsonApiRelationsFormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Category\Http\Management\Actions\CreateAttributesForCategoryAction;
use Modules\Category\Models\Category;
use Spatie\LaravelData\Data;

class CreateProductAttributeCombinedVariationsDto extends Data
{
    public function __construct(
        public readonly string $skuName,
        public readonly int $quantity,
        /** @var Collection<int, AttributesForCombinedValueDto> */
        public readonly Collection $attributes,
        public readonly ?float $price = null,
    ) {}

    public static function fromRequest(JsonApiRelationsFormRequest $request): Collection
    {
        if ($request->relation('product_variations_combinations')->isEmpty()) {
            return collect();
        }

        $category = Category::findOrFail($request->relation('category')['id']);

        $productVariations = $request->relation('product_variations_combinations');

        return DB::transaction(function () use ($category, $productVariations) {
            $createdAttributes = self::prepareAttributeBeforeCollection($category, $productVariations);

            return $productVariations->map(
                function (array $variation) use ($createdAttributes, $category) {
                    return self::from([
                        'skuName' => $variation['sku'],
                        'quantity' => $variation['quantity'],
                        'price' => $variation['price'] ? round($variation['price'], 2) : null,
                        'attributes' => AttributesForCombinedValueDto::collectWithCategory(
                            items: $variation['attributes'],
                            category: $category,
                            createdAttributes: $createdAttributes,
                        ),
                    ]);
                },
            );
        });
    }

    private static function prepareAttributeBeforeCollection(
        Category $category,
        Collection $productVariations,
    ): Collection {
        $attributes = $productVariations->pluck('attributes')->collapse();

        $newAttributes = $attributes
            ->filter(fn ($attribute) => ! array_key_exists('id', $attribute) || $attribute['id'] === null)
            ->unique(fn ($attr) => mb_strtolower($attr['name']));

        return (new CreateAttributesForCategoryAction)->handle(
            $category,
            $newAttributes->map(fn ($attr) => [
                'name' => $attr['name'],
                'type' => $attr['type']['id'],
                'view_type' => $attr['attribute_view_type'],
            ])->values(),
        );
    }
}
