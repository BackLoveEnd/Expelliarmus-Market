<?php

declare(strict_types=1);

namespace Modules\Product\Storages\ProductImages;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Image;
use Modules\Product\Http\Management\Contracts\Storage\LocalProductImagesStorageInterface;
use Modules\Product\Http\Management\Exceptions\FailedToUploadImagesException;
use Modules\Product\Models\Product;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Throwable;

class LocalProductImagesStorage extends BaseProductImagesStorage implements LocalProductImagesStorageInterface
{
    public function upload(UploadedFile $file, Product $product, ?string $hashName = null): string
    {
        try {
            $path = "product-id-$product->id-images";

            $this->storage->makeDirectory($path);

            $fileName = $hashName ?? $file->hashName();

            return $this->storage->putFileAs($path, $file, $fileName);
        } catch (Throwable $e) {
            throw new FileException($e->getMessage());
        }
    }

    /**
     * @throws FailedToUploadImagesException
     */
    public function uploadMany(array $files, Product $product): array
    {
        $images = [];

        if (! $files) {
            $this->storage->copy(
                $this->defaultImageId(),
                $this->getImageFullPath($product, $this->defaultImageId())
            );

            return [$this->defaultImageId()];
        }

        try {
            foreach ($files as $file) {
                $this->upload($file, $product);

                $images[] = $file->hashName();
            }
        } catch (Throwable $e) {
            foreach ($images as $image) {
                $this->delete($product, $this->getImageFullPath($product, $image));
            }

            throw new FailedToUploadImagesException($e->getMessage(), $e);
        }

        return $images;
    }

    public function getOne(Product $product, string $imageId): string
    {
        if ($this->isExists($product, $imageId)) {
            return $this->storage->url($this->getImageFullPath($product, $imageId));
        }

        return $this->storage->url($this->defaultImageId());
    }

    public function getAll(Product $product): array
    {
        if ($product->images === null) {
            return [];
        }

        return collect($product->images)->map(function (string $imageId) use ($product) {
            return $this->getOne($product, $imageId);
        })->toArray();
    }

    public function getAllFromSources(Product $product, array $imagesSources): array
    {
        $images = [];

        foreach ($imagesSources as $imagesSource) {
            $images[] = $this->storage->url($this->getImageFullPath($product, $imagesSource));
        }

        return $images;
    }

    public function delete(Product $product, string $imageId): bool
    {
        return $this->storage->delete($this->getImageFullPath($product, $imageId));
    }

    protected function getImageFullPath(Product $product, string $imageId): string
    {
        return "product-id-$product->id-images/$imageId";
    }

    protected function getInterventionPreviewImage(Product $product, string $imageId): Image
    {
        Log::info($imageId);
        try {
            if ($imageId === $this->defaultPreviewImage()) {
                $imageContent = $this->imageManager->read(
                    storage_path("app/public/products/".$this->defaultPreviewImage())
                );
            } else {
                $imageContent = $this->imageManager->read(
                    storage_path("app/public/products/product-id-$product->id-images/$imageId")
                );
            }
        } catch (Throwable $e) {
            throw new FailedToUploadImagesException($e->getMessage(), $e);
        }

        return $imageContent;
    }
}