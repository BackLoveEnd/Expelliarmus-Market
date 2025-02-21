<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Images;

use Illuminate\Http\UploadedFile;
use Modules\Product\Http\Management\Contracts\Storage\ProductImagesStorageInterface;
use Modules\Product\Http\Management\DTO\ProductImageDto;
use Modules\Product\Models\Product;
use Modules\Product\Storages\ProductImages\Size;

class ProductImagesService
{
    public function __construct(
        private ProductImagesStorageInterface $imagesStorage,
    ) {
    }

    public function upload(ProductImageDto $imageDto, Product $product, Size $size): void
    {
        $images = $this->uploadMainImages($imageDto->mainImages, $product);

        $previewImageSource = $this->uploadPreviewImage($imageDto->previewImage, $product);

        $previewImage = $this->uploadResizedPreviewImage($product, $previewImageSource, $size);

        $product->saveImages([
            'images' => $this->imagesStorage->getAllFromSources($product, $images),
            'preview_image' => $previewImage ? $this->imagesStorage->getResized(
                $product,
                $previewImage,
                $size
            ) : null,
            'preview_image_source' => $previewImageSource
        ]);
    }

    public function uploadMainImages(array $images, Product $product): array
    {
        return $this->imagesStorage->uploadMany($images, $product);
    }

    public function uploadPreviewImage(?UploadedFile $image, Product $product): ?string
    {
        if (! $image) {
            return null;
        }

        $imageSource = 'preview_'.$image->hashName();

        $this->imagesStorage->upload($image, $product, $imageSource);

        return $imageSource;
    }

    public function getResizedImage(Product $product, Size $size): string
    {
        $resizedImage = $this->imagesStorage->getResized($product, $product->preview_image, $size);

        $product->savePreviewImage(
            url: $resizedImage,
            source: $this->formatToResizedImage($product, $size)
        );

        return $resizedImage;
    }

    public function getProductImages(Product $product): array
    {
        return $this->imagesStorage->getAll($product);
    }

    protected function uploadResizedPreviewImage(Product $product, ?string $imageId, Size $size): ?string
    {
        if (! $imageId) {
            $imageId = $this->imagesStorage->defaultPreviewImage();
        }

        return $this->imagesStorage->saveResized(
            product: $product,
            imageId: $imageId,
            size: $size
        );
    }

    protected function formatToResizedImage(Product $product, Size $size): string
    {
        $product->preview_image ??= $this->imagesStorage->defaultPreviewImage();

        return $size->width.'_'.$size->height.'_'.$product->preview_image;
    }
}