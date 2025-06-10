<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Management\Support\ProductSlug;
use Modules\Warehouse\Http\Actions\GetAllDiscountedProductsAction;
use Modules\Warehouse\Http\Actions\GetProductWithDiscountInfoAction;
use Modules\Warehouse\Http\Actions\SearchForProductToAddDiscount;
use Modules\Warehouse\Http\Resources\Discount\DiscountedProductResource;
use Modules\Warehouse\Http\Resources\Discount\ProductWarehouseDiscountsResource;
use Modules\Warehouse\Http\Resources\Warehouse\SearchedProductsSetResource;
use Modules\Warehouse\Models\Warehouse;
use TiMacDonald\JsonApi\JsonApiResourceCollection as Resource;

class RetrieveDiscountController extends Controller
{
    public function __construct()
    {
        $this->authorize('view', Warehouse::class);
    }

    /**
     * Search for products that are available for a discount.
     *
     * Usage place - Admin section.
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
     * Retrieve discounted products.
     *
     * Usage place - Admin section.
     */
    public function getAllDiscountedProducts(GetAllDiscountedProductsAction $action): Resource
    {
        $discountedProducts = $action->handle();

        return DiscountedProductResource::collection($discountedProducts->items())
            ->additional([
                'links' => [
                    'first' => $discountedProducts->url(1),
                    'next' => $discountedProducts->nextPageUrl(),
                    'last' => $discountedProducts->lastPage(),
                    'next_page' => $discountedProducts->hasMorePages() ? $discountedProducts->currentPage() + 1 : null,
                ],
                'meta' => [
                    'total' => $discountedProducts->total(),
                    'per_page' => $discountedProducts->perPage(),
                ],
            ]);
    }

    /**
     * Retrieve product discount information.
     *
     * Usage place - Admin section.
     */
    public function getProductWithDiscountsInfo(
        ProductSlug $productSlug,
        GetProductWithDiscountInfoAction $action,
    ): ProductWarehouseDiscountsResource {
        $product = $action->handle($productSlug->getProductId());

        return ProductWarehouseDiscountsResource::make($product);
    }
}
