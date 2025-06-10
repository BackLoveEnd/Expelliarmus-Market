<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Dto;

use Illuminate\Support\Collection;
use Modules\Order\Cart\Models\Cart;

final class UserCartInfoDto
{
    public function __construct(
        private int $product_id,
        private int $quantity,
        private string $productImage,
        private string $productTitle,
        private string $productSlug,
        private float $pricePerUnit,
        private float $finalPrice,
        private ?array $discount,
        private ?array $variation,
        private ?string $id = null,
    ) {}

    public static function fromArray(array $data): UserCartInfoDto
    {
        return new self(
            product_id: $data['product_id'],
            quantity: $data['quantity'],
            productImage: $data['product_image'],
            productTitle: $data['product_title'],
            productSlug: $data['product_slug'],
            pricePerUnit: $data['price_per_unit'],
            finalPrice: $data['final_price'],
            discount: $data['discount'] ?? null,
            variation: $data['variation'] ?? null,
        );
    }

    public static function fromModels(Collection $carts): Collection
    {
        $carts->loadMissing('product');

        return $carts->map(function (Cart $cart) {
            return (object) [
                'id' => $cart->id,
                'product_image' => $cart->product->preview_image,
                'product_title' => $cart->product->title,
                'product_slug' => $cart->product->slug,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'price_per_unit' => $cart->price_per_unit,
                'final_price' => $cart->final_price,
                'discount' => $cart->discount,
                'variation' => $cart->variation,
            ];
        });
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'product_image' => $this->getProductImage(),
            'product_title' => $this->getProductTitle(),
            'product_slug' => $this->getProductSlug(),
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
        return (object) $this->toArray();
    }

    public function getProductId(): int
    {
        return $this->product_id;
    }

    public function setProductId(int $product_id): void
    {
        $this->product_id = $product_id;
    }

    public function setId(string $id): UserCartInfoDto
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getProductImage(): string
    {
        return $this->productImage;
    }

    public function setProductImage(string $productImage): void
    {
        $this->productImage = $productImage;
    }

    public function getProductTitle(): string
    {
        return $this->productTitle;
    }

    public function setProductTitle(string $productTitle): void
    {
        $this->productTitle = $productTitle;
    }

    public function getProductSlug(): string
    {
        return $this->productSlug;
    }

    public function setProductSlug(string $productSlug): void
    {
        $this->productSlug = $productSlug;
    }
}
