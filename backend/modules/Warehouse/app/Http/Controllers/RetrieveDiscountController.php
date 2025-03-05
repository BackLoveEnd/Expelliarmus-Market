<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Management\Support\ProductSlug;
use Modules\Warehouse\Http\Actions\GetProductWithDiscountInfoAction;
use Modules\Warehouse\Http\Actions\SearchForProductToAddDiscount;
use Modules\Warehouse\Http\Resources\Discount\ProductWarehouseDiscountsResource;
use Modules\Warehouse\Http\Resources\Warehouse\SearchedProductsSetResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection as Resource;

class RetrieveDiscountController extends Controller
{
    /**
     * Search for products that are available for a discount.
     *
     * Usage place - Admin section.
     *
     * @param  Request  $request
     * @param  SearchForProductToAddDiscount  $action
     * @return Resource|JsonResponse
     */
    public function search(Request $request, SearchForProductToAddDiscount $action): Resource|JsonResponse
    {
        $products = $action->handle($request->query('search'));

        if ($products->isEmpty()) {
            return response()->json(['message' => 'Products by this search key not found.'], 404);
        }

        return SearchedProductsSetResource::collection($products);
    }

    /**
     * Retrieve product discount information.
     *
     * Usage place - Admin section.
     *
     * @param  ProductSlug  $productSlug
     * @param  GetProductWithDiscountInfoAction  $action
     * @return ProductWarehouseDiscountsResource
     */
    public function getProductWithDiscountsInfo(
        ProductSlug $productSlug,
        GetProductWithDiscountInfoAction $action,
    ): ProductWarehouseDiscountsResource {
        $product = $action->handle($productSlug->getProductId());

        return ProductWarehouseDiscountsResource::make($product);
    }
}