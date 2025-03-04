<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Management\Support\ProductSlug;
use Modules\Warehouse\Http\Actions\SearchForProductToAddDiscount;
use Modules\Warehouse\Http\Resources\Discount\ProductWarehouseDiscountsResource;
use Modules\Warehouse\Http\Resources\Warehouse\SearchedProductsSetResource;
use Modules\Warehouse\Services\ProductDiscountService;
use TiMacDonald\JsonApi\JsonApiResourceCollection as Resource;

class RetrieveDiscountController extends Controller
{
    public function __construct(
        private ProductDiscountService $discountService,
    ) {}

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
     * @return ProductWarehouseDiscountsResource
     */
    public function getProductWithDiscountsInfo(ProductSlug $productSlug): ProductWarehouseDiscountsResource
    {
        $product = $this->discountService->getProductWithDiscounts($productSlug->getProductId());

        return ProductWarehouseDiscountsResource::make($product);
    }
}