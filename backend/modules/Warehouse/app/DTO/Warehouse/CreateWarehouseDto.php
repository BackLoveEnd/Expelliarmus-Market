<?php

declare(strict_types=1);

namespace Modules\Warehouse\DTO\Warehouse;

use App\Services\Validators\JsonApiRelationsFormRequest;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Dto;

class CreateWarehouseDto extends Dto
{
    public function __construct(
        private int $totalQuantity,
        private readonly ?float $price = null,
        private ?Collection $variationPrices = null,
    ) {}

    public static function fromRequest(JsonApiRelationsFormRequest $request): CreateWarehouseDto
    {
        return new self(
            totalQuantity: (int) $request->total_quantity,
            price: $request->price,
        );
    }

    public function setTotalQuantity(int $totalQuantity): void
    {
        $this->totalQuantity = $totalQuantity;
    }

    public function setVariationPrices(Collection $prices): void
    {
        $this->variationPrices = $prices;
    }

    public function getVariationPrices(): ?Collection
    {
        return $this->variationPrices;
    }

    public function getTotalQuantity(): int
    {
        return $this->totalQuantity;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }
}
