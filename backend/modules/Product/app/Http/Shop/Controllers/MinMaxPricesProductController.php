<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Http\Shop\Actions\Prices\GetMinMaxProductPriceForBrandAction as PriceBrandAction;
use Modules\Product\Http\Shop\Actions\Prices\GetMinMaxProductsPriceAction;
use Modules\Product\Http\Shop\Actions\Prices\GetMinMaxProductsPriceForCategoryAction as PriceCategoryAction;

class MinMaxPricesProductController extends Controller
{
    /**
     * Retrieve min and max prices of all products.
     *
     * Usage place - Shop.
     */
    public function getMinMaxProductsPrice(GetMinMaxProductsPriceAction $action): JsonResponse
    {
        $prices = $action->handle();

        return response()->json([
            'data' => [
                'type' => 'staff',
                'attributes' => [
                    'min_price' => $prices->min_price ?? 0,
                    'max_price' => $prices->max_price ?? 0,
                ],
            ],
        ]);
    }

    /**
     * Retrieve min and max prices of products for a specific category.
     *
     * Usage place - Shop.
     */
    public function getMinMaxProductsPriceForCategory(Category $category, PriceCategoryAction $action): JsonResponse
    {
        $prices = $action->handle($category);

        return response()->json([
            'data' => [
                'type' => 'staff',
                'attributes' => [
                    'min_price' => $prices->min_price ?? 0,
                    'max_price' => $prices->max_price ?? 0,
                ],
            ],
        ]);
    }

    /**
     * Retrieve min and max prices of products for a specific brand.
     *
     * Usage place - Shop.
     */
    public function getMinMaxProductsPriceForBrand(Brand $brand, PriceBrandAction $action): JsonResponse
    {
        $prices = $action->handle($brand);

        return response()->json([
            'data' => [
                'type' => 'staff',
                'attributes' => [
                    'min_price' => $prices->min_price ?? 0,
                    'max_price' => $prices->max_price ?? 0,
                ],
            ],
        ]);
    }
}
