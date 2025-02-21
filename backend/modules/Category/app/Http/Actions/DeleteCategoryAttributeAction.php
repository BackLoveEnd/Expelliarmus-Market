<?php

declare(strict_types=1);

namespace Modules\Category\Http\Actions;

use Modules\Category\Http\Exceptions\AttributeNotRelatedToCategoryException;
use Modules\Category\Http\Exceptions\FailedToDeleteCategoryAttributeException;
use Modules\Category\Models\Category;
use Modules\Warehouse\Models\ProductAttribute;

class DeleteCategoryAttributeAction
{
    /**
     * @throws AttributeNotRelatedToCategoryException|FailedToDeleteCategoryAttributeException
     */
    public function handle(Category $category, ProductAttribute $productAttribute): void
    {
        if (! $category->productAttributes->contains($productAttribute)) {
            throw new AttributeNotRelatedToCategoryException();
        }

        if ($productAttribute->hasUsageInProductsWithCombinedAttributes()
            || $productAttribute->hasUsageInProductsWithSingleAttributes()) {
            throw FailedToDeleteCategoryAttributeException::attributesHasUsageInProducts();
        }

        $productAttribute->delete();
    }
}