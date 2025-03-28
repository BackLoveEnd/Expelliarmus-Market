<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Dto;

final class UserCartInfoDto
{
    public function __construct(
        private int $product_id,
        private int $quantity,
        private float $pricePerUnit,
        private float $finalPrice,
        private ?array $discount,
        private ?array $variation,
    ) {}

    public static function fromArray(array $data): UserCartInfoDto
    {
        return new self(
            product_id: $data['product_id'],
            quantity: $data['quantity'],
            pricePerUnit: $data['price_per_unit'],
            finalPrice: $data['final_price'],
            discount: $data['discount'] ?? null,
            variation: $data['variation'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->getProductId(),
            'quantity' => $this->getQuantity(),
            'price_per_unit' => $this->getPricePerUnit(),
            'final_price' => $this->getFinalPrice(),
            'discount' => $this->getDiscount(),
            'variation' => $this->getVariation(),
        ];
    }

    public function toBase(): object
    {
        return (object)$this->toArray();
    }

    public function getProductId(): int
    {
        return $this->product_id;
    }

    public function setProductId(int $product_id): void
    {
        $this->product_id = $product_id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPricePerUnit(): float
    {
        return $this->pricePerUnit;
    }

    public function setPricePerUnit(float $pricePerUnit): void
    {
        $this->pricePerUnit = $pricePerUnit;
    }

    public function getFinalPrice(): float
    {
        return $this->finalPrice;
    }

    public function setFinalPrice(float $finalPrice): void
    {
        $this->finalPrice = $finalPrice;
    }

    public function getVariation(): ?array
    {
        return $this->variation;
    }

    public function setVariation(?array $variation): void
    {
        $this->variation = $variation;
    }

    public function getDiscount(): ?array
    {
        return $this->discount;
    }

    public function setDiscount(?array $discount): void
    {
        $this->discount = $discount;
    }
}