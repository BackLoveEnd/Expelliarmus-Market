<?php

declare(strict_types=1);

namespace Modules\Product\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Modules\Product\Models\Product;

class ProductImagesExistsRule implements ValidationRule
{
    public function __construct(
        private int $productId,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $imagesId = collect($value)
            ->filter(fn (array $image) => $image['id'] && $image['id'] !== null)
            ->pluck('id');

        foreach ($imagesId as $image) {
            if (! Str::isUuid($image)) {
                $fail('Invalid image id');

                return;
            }
        }

        $productImages = Product::query()
            ->where('id', $this->productId)
            ->first(['images']);

        if (! $productImages->images) {
            return;
        }

        $productImages = collect($productImages->images)->pluck('id');

        if ($imagesId->diff($productImages)->isNotEmpty()) {
            $fail('Some images do not belong to this product.');
        }
    }
}
