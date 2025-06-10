<?php

declare(strict_types=1);

namespace Modules\Product\Storages\ProductImages;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Intervention\Image\Image;
use Modules\Product\Http\Management\Contracts\Storage\LocalProductImagesStorageInterface;
use Modules\Product\Http\Management\DTO\Images\MainImageDto;
use Modules\Product\Http\Management\Exceptions\FailedToRetrieveImagesException;
use Modules\Product\Http\Management\Exceptions\FailedToUploadImagesException;
use Modules\Product\Models\Product;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Throwable;

class LocalProductImagesStorage extends BaseProductImagesStorage implements LocalProductImagesStorageInterface
{
    private array $images = [];

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
    public function uploadMany(Collection $files, Product $product): array
    {
        if ($files->isEmpty()) {
            $this->storage->copy(
                $this->defaultImageId(),
                $this->getImageFullPath($product, $this->defaultImageId()),
            );

            return [
                [
                    'order' => 1,
                    'source' => $this->defaultImageId(),
                ],
            ];
        }

        try {
            $this->images = $files->map(function (MainImageDto $image) use ($product) {
                $this->upload($image->image, $product);

                return [
                    'id' => $image->id,
                    'order' => $image->order,
                    'source' => $image->image->hashName(),
                ];
            })->toArray();
        } catch (Throwable $e) {
            foreach ($this->images as $image) {
                $this->delete($product, $this->getImageFullPath($product, $image));
            }

            throw new FailedToUploadImagesException($e->getMessage(), $e);
        }

        return $this->images;
    }

    public function getOne(Product $product, ?string $imageId): string
    {
        if (! $imageId || ! $this->isExists($product, $imageId)) {
            return $this->storage->url($this->defaultImageId());
        }

        return $this->storage->url($this->getImageFullPath($product, $imageId));
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
        try {
            return collect($imagesSources)->map(function (array $images) use ($product) {
                return [
                    ...$images,
                    'image_url' => $this->storage->url($this->getImageFullPath($product, $images['source'])),
                ];
            })->toArray();
        } catch (Throwable $e) {
            throw new FailedToRetrieveImagesException($e->getMessage(), $e);
        }
    }

    public function delete(Product $product, string $imageId): bool
    {
        return $this->storage->delete($this->getImageFullPath($product, $imageId));
    }

    public function deleteMany(Product $product, Collection $sources): void
    {
        $sources->each(function (string $source) use ($product) {
            $this->delete($product, $source);
        });
    }

    protected function getImageFullPath(Product $product, string $imageId): string
    {
        return "product-id-$product->id-images/$imageId";
    }

    protected function getInterventionPreviewImage(Product $product, string $imageId): Image
    {
        try {
            if ($imageId === $this->defaultPreviewImage()) {
                $this->storage->copy(
                    $imageId,
                    $this->getImageFullPath($product, $imageId),
                );
            }

            $imageContent = $this->imageManager->read(
                storage_path("app/public/products/product-id-$product->id-images/$imageId"),
            );
        } catch (Throwable $e) {
            throw new FailedToUploadImagesException($e->getMessage(), $e);
        }

        return $imageContent;
    }
}
