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
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $images = collect($value)->filter(fn(array $image) => $image['id'] && $image['id'] !== null)
            ->pluck('id');

        $images->each(function ($value) use($fail) {
            if (! Str::isUuid($value)) {
                $fail('Invalid image id');
            }
        });

        $productImages = Product::query()
            ->where('id', $this->productId)
            ->first(['images']);

        $productImages = collect($productImages->images)->pluck('id');

        if ($images->diff($productImages)->isNotEmpty()) {
            $fail('Some images do not belong to this product.');
        }
    }
}