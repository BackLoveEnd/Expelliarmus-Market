<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Attributes\Handlers;

use Modules\Product\Http\Management\Service\Attributes\Interfaces\ProductAttributeRetrieveInterface;
use Modules\Product\Models\Product;
use RuntimeException;

class ProductAttributeService
{
    private ?Product $product = null;

    private ?ProductAttributeRetrieveInterface $retrieveStrategy = null;

    private ?ProductAttributeHandler $attributeHandler = null;

    public function __construct(
        private array $variationCols = ['*'],
        private array $attributeCols = ['*']
    ) {
    }

    public function getAttributeHandler(): ProductAttributeHandler
    {
        return $this->attributeHandler;
    }

    public function setAttributesColumns(array $variationCols, array $attributeCols): ProductAttributeHandler
    {
        $this->variationCols = $variationCols;

        $this->attributeCols = $attributeCols;

        return $this->makeAttributeHandler();
    }

    public function setProduct(Product $product): ProductAttributeHandler
    {
        $this->product = $product;

        return $this->makeAttributeHandler();
    }

    public function __call(string $name, array $arguments)
    {
        return $this->getAttributeHandler()->$name(...$arguments);
    }

    protected function makeAttributeHandler(): ProductAttributeHandler
    {
        if (! $this->product) {
            throw new RuntimeException('Product must be set. Use setProduct method.');
        }

        $this->setRetrieveStrategy($this->product);

        $this->attributeHandler = new ProductAttributeHandler(
            product: $this->product,
            retrieveStrategy: $this->retrieveStrategy
        );

        return $this->getAttributeHandler();
    }

    protected function setRetrieveStrategy(Product $product): void
    {
        $this->retrieveStrategy = $product->hasCombinedAttributes()
            ? new CombinedAttributeRetrieveService($this->variationCols, $this->attributeCols)
            : new SingleAttributeRetrieveService($this->variationCols, $this->attributeCols);
    }
}