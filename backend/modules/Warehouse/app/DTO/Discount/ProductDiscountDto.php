<?php

declare(strict_types=1);

namespace Modules\Warehouse\DTO\Discount;

use App\Services\Validators\JsonApiFormRequest;
use Carbon\Carbon;

final readonly class ProductDiscountDto
{
    public function __construct(
        public int $percentage,
        public Carbon $endAt,
        public ?Carbon $startFrom = null,
        public ?int $variationId = null,
    ) {}

    public static function fromRequest(JsonApiFormRequest $request): ProductDiscountDto
    {
        return new self(
            percentage: $request->percentage,
            endAt: Carbon::parse($request->end_date)->setTimezone(config('app.timezone')),
            startFrom: $request->start_date ? Carbon::parse($request->start_date)
                ->setTimezone(config('app.timezone')) : null,
            variationId: $request->variation,
        );
    }
}
