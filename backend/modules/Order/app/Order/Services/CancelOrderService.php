<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\DB;
use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\Order\Order\Exceptions\CannotChangeOrderStatusException;
use Modules\Order\Order\Exceptions\FailedToCancelOrderException;
use Modules\Order\Order\Http\Actions\ChangeOrderStatusAction;
use Modules\Order\Order\Models\Order;
use Modules\Order\Order\Models\OrderLine;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;
use Throwable;

class CancelOrderService
{
    public function __construct(
        private ChangeOrderStatusAction $changeOrderStatusAction,
        private WarehouseStockService $warehouseStockService,
        private WarehouseProductInfoService $warehouseInfoService,
    ) {}

    public function cancel(Order $order): void
    {
        try {
            DB::transaction(function () use ($order) {
                $this->changeOrderStatusAction->handle($order, OrderStatusEnum::CANCELED);

                $orderLines = $order
                    ->orderLines()
                    ->with(['product' => fn ($query) => $query->select(['id', 'with_attribute_combinations'])])
                    ->get(['order_id', 'quantity', 'product_id', 'variation']);

                $this->warehouseStockService->returnReservedProductsToStock(
                    productsWithQuantities: $this->loadVariationsToProducts($orderLines),
                );
            });
        } catch (Throwable $e) {
            if (! $e instanceof CannotChangeOrderStatusException) {
                throw new FailedToCancelOrderException($e->getMessage());
            }
        }
    }

    private function loadVariationsToProducts(Collection $orderLines): BaseCollection
    {
        $productsWithVariationIds = $orderLines->map(function (OrderLine $line) {
            return [
                'product' => $line->product,
                'variation_id' => $line->variation ? $line->variation['id'] : null,
                'quantity' => $line->quantity,
            ];
        });

        return $this->warehouseInfoService->getProductsAttributeById(
            productsWithVariationId: $productsWithVariationIds,
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [['id', 'quantity'], []],
                combinedAttrCols: [['id', 'quantity'], []],
                warehouseCols: ['id', 'product_id', 'total_quantity'],
            ),
        );
    }
}
