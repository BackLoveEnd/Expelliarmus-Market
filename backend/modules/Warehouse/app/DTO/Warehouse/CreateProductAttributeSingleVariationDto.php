<?php

declare(strict_types=1);

namespace Modules\Warehouse\DTO\Warehouse;

use App\Services\Validators\JsonApiRelationsFormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Category\Http\Management\Actions\CreateAttributesForCategoryAction;
use Modules\Category\Models\Category;
use Modules\Warehouse\Models\ProductAttribute;
use Spatie\LaravelData\Data;

class CreateProductAttributeSingleVariationDto extends Data
{
    public function __construct(
        /** @var Collection<int, AttributesForSingleValueDto> */
        public readonly Collection $attributes,
        public readonly ?int $attributeId = null,
    ) {}

    public static function fromRequest(JsonApiRelationsFormRequest $request
    ): ?CreateProductAttributeSingleVariationDto {
        $attributes = $request->relation('product_variation');

        if ($attributes->isEmpty()) {
            return null;
        }

        $category = Category::findOrFail($request->relation('category')['id']);

        if (! array_key_exists('attribute_id',
            $attributes[0]) || $attributes[0]['attribute_id'] === null) {
            $newAttribute = (new CreateAttributesForCategoryAction)->handle($category, collect([
                'name' => $attributes[0]['attribute_name'],
                'type' => $attributes[0]['attribute_type'],
                'view_type' => $attributes[0]['attribute_view_type'],
            ]));

            self::ensureRequiredAttributePresent($category, $newAttribute->id);

            return self::from([
                'attributes' => collect(AttributesForSingleValueDto::collect($attributes[0]['attributes'])),
                'attributeId' => $newAttribute->id,
            ]);
        }

        self::ensureRequiredAttributePresent($category, $attributes[0]['attribute_id']);

        return self::from([
            'attributes' => collect(AttributesForSingleValueDto::collect($attributes[0]['attributes'])),
            'attributeId' => $attributes[0]['attribute_id'],
        ]);
    }

    private static function ensureRequiredAttributePresent(Category $category, int $attributeId): void
    {
        $attributes = $category
            ->allAttributesFromTree()
            ->filter(fn (ProductAttribute $productAttribute) => $productAttribute->required);

        if ($attributes->count() > 1) {
            throw ValidationException::withMessages([
                'single_attributes' => 'Selected category contains more than 1 required attribute. Please create attributes combinations.',
            ]);
        }

        if ($attributes->isNotEmpty() && ! $attributes->contains('id', $attributeId)) {
            throw ValidationException::withMessages([
                'single_attributes' => 'Combination must have all required attributes. See more in category section.',
            ]);
        }
    }
}
