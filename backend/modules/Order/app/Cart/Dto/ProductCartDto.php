<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Dto;

use App\Services\Validators\JsonApiFormRequest;
use Modules\Product\Models\Product;

final readonly class ProductCartDto
{
    public function __construct(
        public Product $product,
        public ?int $variationId,
        public int $quantity,
    ) {}

    public static function fromRequest(JsonApiFormRequest $request): ProductCartDto
    {
        return new self(
            product: Product::query()->with('warehouse')->find(
                $request->product_id,
                [
                    'id',
                    'title',
                    'with_attribute_combinations',
                    'status',
                    'preview_image',
                    'slug',
                ],
            ),
            variationId: $request->variation_id,
            quantity: $request->quantity,
        );
    }
}
