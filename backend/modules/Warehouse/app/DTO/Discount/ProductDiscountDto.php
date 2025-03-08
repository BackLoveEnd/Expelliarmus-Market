<?php

declare(strict_types=1);

namespace Modules\Warehouse\DTO\Discount;

use App\Services\Validators\JsonApiFormRequest;
use Carbon\Carbon;

final readonly class ProductDiscountDto
{
    public function __construct(
        public int $percentage,
        public Carbon $startFrom,
        public Carbon $endAt,
        public ?int $variationId = null,
    ) {}

    public static function fromRequest(JsonApiFormRequest $request): ProductDiscountDto
    {
        return new self(
            percentage: $request->percentage,
            startFrom: Carbon::parse($request->start_date)->setTimezone(config('app.timezone')),
            endAt: Carbon::parse($request->end_date)->setTimezone(config('app.timezone')),
            variationId: $request->variation,
        );
    }
}