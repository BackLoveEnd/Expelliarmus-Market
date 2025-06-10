<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Modules\Product\Models\Product;

class DeleteVariationsWhenNeedAction
{
    public function handle(Product $product, ?bool $newVariationType): void
    {
        $oldVariationType = $product->hasCombinedAttributes(true);

        if ($oldVariationType === null && $newVariationType === null) {
            return;
        }

        match (true) {
            $oldVariationType && ! $newVariationType => $this->deleteCombinedVariations($product),
            ! $oldVariationType && $newVariationType => $this->deleteSingleVariation($product),
            $newVariationType === null => $this->deleteAllVariations($product),
            default => null
        };
    }

    private function deleteCombinedVariations(Product $product): void
    {
        $product->combinedAttributes()->delete();
    }

    private function deleteSingleVariation(Product $product): void
    {
        $product->singleAttributes()->delete();
    }

    private function deleteAllVariations(Product $product): void
    {
        $this->deleteCombinedVariations($product);
        $this->deleteSingleVariation($product);
    }
}
