<?php

declare(strict_types=1);

namespace Modules\Warehouse\DTO;

use Carbon\Carbon;
use Modules\Warehouse\Http\Requests\AddDiscountToProductRequest;

final readonly class ProductDiscountDto
{
    public function __construct(
        public int $percentage,
        public Carbon $startFrom,
        public Carbon $endAt,
        public ?int $variationId = null,
    ) {}

    public static function fromRequest(AddDiscountToProductRequest $request): ProductDiscountDto
    {
        return new self(
            percentage: $request->percentage,
            startFrom: Carbon::parse($request->start_date),
            endAt: Carbon::parse($request->end_date),
            variationId: $request->variation,
        );
    }
}