<?php

namespace Modules\Order\Cart\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Product\Models\Product;

class ProductCartRule implements ValidationRule
{
    public function __construct(
        private int|string|null $productId,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $product = Product::query()
            ->where('id', (int) $this->productId)
            ->firstOrFail(['id', 'with_attribute_combinations']);

        if (is_null($product->hasCombinedAttributes())) {
            return;
        }

        if ($product->hasCombinedAttributes()) {
            if (! $product->combinedAttributes()->where('id', $value)->exists()) {
                $fail('Variation is not related to product.');
            }
        } elseif (! $product->singleAttributes()->where('id', $value)->exists()) {
            $fail('Variation is not related to product.');
        }
    }
}
