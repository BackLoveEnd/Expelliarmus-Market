<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\ProductDiscountDto;
use Modules\Warehouse\Http\Requests\AddDiscountToProductRequest;
use Modules\Warehouse\Services\ProductDiscountService;

class DiscountController extends Controller
{
    public function __construct(
        private ProductDiscountService $discountService,
    ) {}

    /**
     * Add discount to product.
     *
     * Usage place - Admin section.
     *
     * @param  AddDiscountToProductRequest  $request
     * @param  Product  $product
     * @return JsonResponse
     */
    public function addDiscount(AddDiscountToProductRequest $request, Product $product): JsonResponse
    {
        $this->discountService->addDiscount(
            product: $product,
            dto: ProductDiscountDto::fromRequest($request),
        );

        return response()->json(['message' => 'Discount was added successfully.']);
    }
}