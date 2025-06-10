<?php

declare(strict_types=1);

namespace Modules\Order\Order\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ProductHasNotEnoughSuppliesException extends Exception
{
    public static function fromProductArticle(string $productArticle): static
    {
        return new static($productArticle);
    }

    public function render(): JsonResponse
    {
        return response()->json(
            ['message' => "Product $this->message has not enough supplies for requested quantities."],
            409,
        );
    }
}
