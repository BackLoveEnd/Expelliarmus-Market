<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Discount\ProductDiscountDto;
use Modules\Warehouse\Enums\ProductStatusEnum as Status;
use Modules\Warehouse\Http\Exceptions\DiscountIsNotRelatedToProductException;
use Modules\Warehouse\Http\Requests\AddDiscountToProductRequest;
use Modules\Warehouse\Http\Requests\EditDiscountRequest;
use Modules\Warehouse\Models\Discount;
use Modules\Warehouse\Services\Discount\ProductDiscountServiceFactory;

class DiscountController extends Controller
{
    public function __construct(
        private ProductDiscountServiceFactory $discountService,
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
        if ($product->status->is(Status::PUBLISHED)
            || $product->status->is(Status::NOT_PUBLISHED)
        ) {
            return response()->json([
                'message' => 'Adding discount allowed only for published or not published products',
            ],
                403);
        }

        $this->discountService
            ->addDiscount($product)
            ->process(ProductDiscountDto::fromRequest($request));

        return response()->json(['message' => 'Discount was added successfully.']);
    }

    /**
     * Edit discount information.
     *
     * Usage place - Admin section.
     *
     * @param  EditDiscountRequest  $request
     * @param  Product  $product
     * @param  Discount  $discount
     * @return JsonResponse
     */
    public function editDiscount(EditDiscountRequest $request, Product $product, Discount $discount): JsonResponse
    {
        $this->discountService
            ->editDiscount($product, $discount)
            ->process(ProductDiscountDto::fromRequest($request));

        return response()->json(['message' => 'Discount was updated.']);
    }

    /**
     * Cancel product discount.
     *
     * Usage place - Admin section.
     *
     * @param  Product  $product
     * @param  Discount  $discount
     * @return JsonResponse
     * @throws DiscountIsNotRelatedToProductException
     */
    public function cancelDiscount(Product $product, Discount $discount): JsonResponse
    {
        $this->discountService->cancelDiscount($product, $discount)->process();

        return response()->json(['message' => 'Discount was cancelled.']);
    }
}
