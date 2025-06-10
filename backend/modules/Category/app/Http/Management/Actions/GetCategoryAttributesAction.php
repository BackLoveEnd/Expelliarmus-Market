<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Actions;

use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Warehouse\Models\ProductAttribute;

class GetCategoryAttributesAction
{
    public function handle(Category $category): Collection
    {
        return collect($category->allAttributesFromTree())->map(function (ProductAttribute $attribute) {
            return (object) [
                'id' => $attribute->id,
                'category_id' => $attribute->category_id,
                'name' => $attribute->name,
                'type' => (object) [
                    'id' => $attribute->type->value,
                    'name' => $attribute->type->toTypes(),
                ],
                'required' => $attribute->required,
            ];
        });
    }
}
