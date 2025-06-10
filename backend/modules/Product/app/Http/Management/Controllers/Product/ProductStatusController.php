<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Controllers\Product;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Modules\Product\Http\Management\Service\Product\ProductStatusService;
use Modules\Product\Models\Product;

class ProductStatusController extends Controller
{
    public function __construct(private ProductStatusService $statusService) {}

    /**
     * Set product as published.
     *
     * Usage - Admin section.
     *
     * @throws Exception
     */
    public function publish(Product $product): JsonResponse
    {
        $this->authorize('publish', Product::class);

        $this->statusService->publish($product);

        return response()->json(['message' => 'Product was successfully published.']);
    }

    /**
     * Set product as unpublished.
     *
     * Usage place - Admin section.
     *
     * @throws Exception
     */
    public function unPublish(Product $product): JsonResponse
    {
        $this->authorize('publish', Product::class);

        $this->statusService->unPublish($product);

        return response()->json(['message' => 'Product was successfully unpublished.']);
    }
}
