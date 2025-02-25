<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Product\Http\Management\Actions\Product\Create\CreateProduct;
use Modules\Product\Http\Management\Actions\Product\Create\CreateProductFactoryAction;
use Modules\Product\Http\Management\Actions\Product\Edit\DeleteVariationsWhenNeedAction;
use Modules\Product\Http\Management\Actions\Product\Edit\EditProduct;
use Modules\Product\Http\Management\Actions\Product\Edit\EditProductFactoryAction;
use Modules\Product\Http\Management\Actions\Product\Edit\MoveProductToTrashAction;
use Modules\Product\Http\Management\Exceptions\CannotTrashPublishedProductException;
use Modules\Product\Http\Management\Requests\ProductCreateRequest;
use Modules\Product\Http\Management\Requests\ProductEditRequest;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum as Status;
use Modules\Warehouse\Http\Actions\CreateProductInWarehouse;
use Modules\Warehouse\Http\Actions\EditProductInWarehouse;

class ManipulationProductController extends Controller
{

    /**
     * Store product data.
     *
     * Usage place - Admin section.
     *
     * @param  ProductCreateRequest  $request
     * @param  CreateProductFactoryAction  $factory
     * @return JsonResponse
     */
    public function store(ProductCreateRequest $request, CreateProductFactoryAction $factory): JsonResponse
    {
        $product = $factory->createAction($request)->handle(
            new CreateProduct(),
            new CreateProductInWarehouse(),
        );

        return response()->json([
            'message' => 'Product successfully created.',
            'data' => [
                'id' => $product->id,
                'type' => 'products',
            ],
        ], 201);
    }

    /**
     * Update product information.
     *
     * Usage place - Admin section.
     *
     * @param  ProductEditRequest  $request
     * @param  Product  $product
     * @param  EditProductFactoryAction  $factory
     * @return JsonResponse
     */
    public function edit(ProductEditRequest $request, Product $product, EditProductFactoryAction $factory): JsonResponse
    {
        if ($product->status->is(Status::PUBLISHED) || $product->status->is(Status::TRASHED)) {
            return response()->json([
                'message' => 'Editing published or trashed product is not allowed.',
            ], 403);
        }

        $factory->createAction($request)->handle(
            new EditProduct($product, new DeleteVariationsWhenNeedAction()),
            new EditProductInWarehouse($product),
        );

        return response()->json(['Product was updated successfully.']);
    }

    /**
     * Move product to trash.
     *
     * Usage place - Admin section.
     *
     * @param  Product  $product
     * @param  MoveProductToTrashAction  $action
     * @return JsonResponse
     * @throws CannotTrashPublishedProductException
     */
    public function moveToTrash(Product $product, MoveProductToTrashAction $action): JsonResponse
    {
        if ($product->trashed() || $product->status->is(Status::TRASHED)) {
            return response()->json(['message' => 'Product is already in trash.'], 409);
        }

        $action->handle($product);

        return response()->json(['message' => 'Product was moved to trash.']);
    }
}