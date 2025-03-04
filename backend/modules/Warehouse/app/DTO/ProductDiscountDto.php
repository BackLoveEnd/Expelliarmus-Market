<?php

declare(strict_types=1);

namespace Modules\Warehouse\DTO;

use Carbon\Carbon;
use Modules\Warehouse\Http\Requests\AddDiscountToProductRequest;

final readonly class ProductDiscountDto
{
    public function __construct(
        public int $variationId,
        public int $percentage,
        public Carbon $startFrom,
        public Carbon $endAt,
    ) {}

    public static function fromRequest(AddDiscountToProductRequest $request): ProductDiscountDto
    {
        return new self(
            variationId: $request->variation,
            percentage: $request->percentage,
            startFrom: Carbon::parse($request->start_date),
            endAt: Carbon::parse($request->end_date),
        );
    }
}