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
        private array $attributeCols = ['*'],
    ) {}

    public function getAttributeHandler(): ProductAttributeHandler
    {
        return $this->attributeHandler;
    }

    public function combinedHandler(): ProductAttributeHandler
    {
        return new ProductAttributeHandler(
            new CombinedAttributeRetrieveService($this->variationCols, $this->attributeCols),
        );
    }

    public function singleHandler(): ProductAttributeHandler
    {
        return new ProductAttributeHandler(
            new SingleAttributeRetrieveService($this->variationCols, $this->attributeCols),
        );
    }

    public function setAttributesColumns(array $variationCols, array $attributeCols): static
    {
        $this->variationCols = $variationCols;

        $this->attributeCols = $attributeCols;

        return $this;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function __call(string $name, array $arguments)
    {
        if (! $this->attributeHandler) {
            $this->makeAttributeHandler();
        }

        return $this->getAttributeHandler()->$name(...$arguments);
    }

    protected function makeAttributeHandler(): ProductAttributeHandler
    {
        if (! $this->product) {
            throw new RuntimeException('Product must be set. Use setProduct method.');
        }

        $this->setRetrieveStrategy($this->product);

        $this->attributeHandler = new ProductAttributeHandler($this->retrieveStrategy);

        return $this->getAttributeHandler()->setProduct($this->product);
    }

    protected function setRetrieveStrategy(Product $product): void
    {
        $this->retrieveStrategy = $product->hasCombinedAttributes()
            ? new CombinedAttributeRetrieveService($this->variationCols, $this->attributeCols)
            : new SingleAttributeRetrieveService($this->variationCols, $this->attributeCols);
    }
}
