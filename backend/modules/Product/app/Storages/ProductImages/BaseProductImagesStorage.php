<?php

declare(strict_types=1);

namespace Modules\Product\Storages\ProductImages;

use Illuminate\Contracts\Filesystem\Filesystem;
use Intervention\Image\Image;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Modules\Product\Http\Management\Contracts\Storage\ProductImagesStorageInterface;
use Modules\Product\Http\Management\Exceptions\FailedToUploadImagesException;
use Modules\Product\Models\Product;
use Throwable;

abstract class BaseProductImagesStorage implements ProductImagesStorageInterface
{
    public function __construct(
        protected ImageManagerInterface $imageManager,
        protected Filesystem $storage,
    ) {}

    public function saveResized(Product $product, string $imageId, Size $size): string
    {
        try {
            $image = $this->getInterventionPreviewImage($product, $imageId)->resize($size->width, $size->height);

            $resizedImageId = $this->getResizedImageId($imageId, $size->width, $size->height);

            $this->storage->put(
                $this->getImageFullPath($product, $resizedImageId),
                (string) $image->encode(),
            );

            return $resizedImageId;
        } catch (Throwable $e) {
            throw new FailedToUploadImagesException($e->getMessage(), $e);
        }
    }

    public function getResized(Product $product, ?string $resizedImageId, Size $size): string
    {
        if (! $resizedImageId || ! $this->isExists($product, $resizedImageId)) {
            $resizedImageId = $this->defaultResizedPreviewImage();

            $this->storage->copy(
                $resizedImageId,
                $this->getImageFullPath($product, $resizedImageId),
            );
        }

        return $this->getOne($product, $resizedImageId);
    }

    public function isExists(Product $product, string $imageId): bool
    {
        return $this->storage->exists($this->getImageFullPath($product, $imageId));
    }

    public function defaultImageId(): string
    {
        return config('product.image.default');
    }

    public function defaultPreviewImage(): string
    {
        return config('product.image.default_preview');
    }

    public function defaultResizedPreviewImage(): string
    {
        return config('product.image.default_resized_preview');
    }

    private function getResizedImageId(string $imageId, int $width, int $height): string
    {
        return $width.'_'.$height.'_'.$imageId;
    }

    abstract protected function getInterventionPreviewImage(Product $product, string $imageId): Image;

    abstract protected function getImageFullPath(Product $product, string $imageId): string;
}
