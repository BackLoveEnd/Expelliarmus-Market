<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Images;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Product\Http\Management\Contracts\Storage\ProductImagesStorageInterface;
use Modules\Product\Http\Management\DTO\Images\MainImageDto;
use Modules\Product\Http\Management\DTO\Images\ProductImageDto;
use Modules\Product\Models\Product;
use Modules\Product\Storages\ProductImages\Size;

class ProductImagesService
{
    public function __construct(
        private ProductImagesStorageInterface $imagesStorage,
    ) {}

    public function upload(ProductImageDto $imageDto, Product $product, Size $size): void
    {
        $images = $this->imagesStorage->uploadMany($imageDto->mainImages, $product);

        $previewImageSource = $this->uploadPreviewImage($imageDto->previewImage, $product);

        $previewImage = $this->uploadResizedPreviewImage($product, $previewImageSource, $size);

        $images = collect($images)
            ->map(fn (array $image) => [...$image, 'id' => Str::uuid7()->toString()])
            ->toArray();

        $product->saveImages([
            'images' => $this->imagesStorage->getAllFromSources($product, $images),
            'preview_image' => $previewImage ? $this->imagesStorage->getResized(
                $product,
                $previewImage,
                $size,
            ) : null,
            'preview_image_source' => $previewImageSource ?? $this->imagesStorage->defaultResizedPreviewImage(),
        ]);
    }

    public function uploadEdit(ProductImageDto $imageDto, Product $product, Size $size): void
    {
        $updatedImages = $this->syncEditImages($product, $imageDto);

        $images['images'] = $this->prepareUpdatedImageForDb($updatedImages, $product);

        if ($imageDto->previewImage) {
            $images['preview_image_source'] = $this->uploadPreviewImage($imageDto->previewImage, $product);

            $resizedPreviewImage = $this->uploadResizedPreviewImage(
                product: $product,
                imageId: $images['preview_image_source'],
                size: $size,
            );

            $this->imagesStorage->delete($product, $product->preview_image_source);

            $this->imagesStorage->delete($product, $this->formatToResizedImage($product, $size));

            $images['preview_image'] = $this->imagesStorage->getResized(
                product: $product,
                resizedImageId: $resizedPreviewImage,
                size: $size,
            );
        }

        $product->saveImages($images);
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
            source: $this->formatToResizedImage($product, $size),
        );

        return $resizedImage;
    }

    public function getProductImages(Product $product): array
    {
        return $this->imagesStorage->getAll($product);
    }

    protected function syncEditImages(Product $product, ProductImageDto $imageDto): Collection
    {
        $newImages = $this->getNewImages($imageDto->mainImages);

        $changedImages = $this->getImagesThatExistsAndWannaChange($imageDto->mainImages);

        $unTouchedImages = $this->getUntouchedImages($imageDto->mainImages);

        $imagesToDelete = collect($product->images)
            ->filter(fn ($image) => ! $imageDto->mainImages->pluck('id')->contains($image['id'])
                || $changedImages->pluck('id')->contains($image['id']),
            );

        $newImages = $newImages->map(fn ($image) => new MainImageDto(
            order: $image->order,
            id: Str::uuid7()->toString(),
            image: $image->image,
        ));

        if ($newImages->isNotEmpty() || $changedImages->isNotEmpty()) {
            $this->imagesStorage->uploadMany($newImages->merge($changedImages), $product);
        }

        if ($imagesToDelete->isNotEmpty()) {
            $this->imagesStorage->deleteMany(
                product: $product,
                sources: $imagesToDelete->whereNotNull('source')->pluck('source'),
            );
        }

        return $newImages
            ->merge($changedImages)->merge($unTouchedImages)
            ->sortBy('order')
            ->values();
    }

    protected function prepareUpdatedImageForDb(Collection $updatedImages, Product $product): array
    {
        if ($updatedImages->isEmpty()) {
            $images = [
                [
                    'id' => Str::uuid7()->toString(),
                    'image_url' => $this->imagesStorage->getOne($product, null),
                    'order' => 1,
                    'source' => $this->imagesStorage->defaultImageId(),
                ],
            ];
        } else {
            $images = $updatedImages->map(fn (MainImageDto $dto) => [
                'id' => $dto->id,
                'image_url' => $dto->existImageUrl ?? $this->imagesStorage->getOne($product, $dto->image?->hashName()),
                'order' => $dto->order,
                'source' => $dto->image ? $dto->image->hashName() : $product->images[$dto->id]['source'] ?? null,
            ])->toArray();
        }

        return $images;
    }

    protected function getImagesThatExistsAndWannaChange(Collection $images): Collection
    {
        return $images->filter(function (MainImageDto $dto) {
            return $dto->id !== null && $dto->image !== null;
        });
    }

    protected function getNewImages(Collection $images): Collection
    {
        return $images->filter(function (MainImageDto $dto) {
            return $dto->image !== null && $dto->id === null;
        });
    }

    protected function getUntouchedImages(Collection $images): Collection
    {
        return $images->filter(function (MainImageDto $dto) {
            return $dto->id !== null && $dto->image === null && $dto->existImageUrl !== null;
        });
    }

    protected function uploadResizedPreviewImage(Product $product, ?string $imageId, Size $size): string
    {
        if (! $imageId) {
            $imageId = $this->imagesStorage->defaultPreviewImage();
        }

        return $this->imagesStorage->saveResized(
            product: $product,
            imageId: $imageId,
            size: $size,
        );
    }

    protected function formatToResizedImage(Product $product, Size $size): string
    {
        $product->preview_image_source ??= $this->imagesStorage->defaultPreviewImage();

        return $size->width.'_'.$size->height.'_'.$product->preview_image_source;
    }
}
