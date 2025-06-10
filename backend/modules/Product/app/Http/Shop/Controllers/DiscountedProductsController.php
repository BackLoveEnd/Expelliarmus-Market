<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Warehouse\Http\Resources\Discount\DiscountedProductResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class DiscountedProductsController extends Controller
{
    public function __construct(private DiscountedProductsService $service) {}

    /**
     * Retrieve products on flash sales.
     *
     * Usage place - Shop.
     */
    public function getFlashSales(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $flashSales = $this->service->getFlashSalesPaginated(
            limit: (int) $request->query('limit', config('product.retrieve.flash-sales')),
            offset: (int) $request->query('offset', 0),
        );

        if ($flashSales->items->isEmpty()) {
            return response()->json(['message' => 'Products with active sales not found.'], 404);
        }

        return DiscountedProductResource::collection($flashSales->items)
            ->additional($flashSales->wrapMeta());
    }
}
