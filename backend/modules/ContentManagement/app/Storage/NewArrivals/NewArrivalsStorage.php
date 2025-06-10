<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Storage\NewArrivals;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Modules\ContentManagement\Http\Dto\NewArrivals\ArrivalContentDto;
use Modules\ContentManagement\Http\Exceptions\FailedToUploadContentException;
use Modules\ContentManagement\Http\Exceptions\ImageNotFoundException;
use Modules\ContentManagement\Models\NewArrival;
use Throwable;

class NewArrivalsStorage
{
    protected Filesystem $storage;

    private array $uploadedImages = [];

    public function __construct(FilesystemManager $filesystemManager)
    {
        $this->storage = $filesystemManager->disk('public_content');
    }

    public function uploadMany(Collection $arrivals): void
    {
        try {
            $arrivals->each(function (ArrivalContentDto $dto) {
                $this->uploadedImages[] = $this->upload($dto->file);
            });
        } catch (Throwable $e) {
            foreach ($this->uploadedImages as $image) {
                $this->delete($image);
            }

            throw new FailedToUploadContentException($e->getMessage());
        }
    }

    public function upload(UploadedFile $file, ?string $hashName = null): false|string
    {
        $this->storage->makeDirectory('arrivals');

        $fileName = $hashName ?? $file->hashName();

        return $this->storage->putFileAs('arrivals', $file, $fileName);
    }

    public function getImageUrl(string $imageId): string
    {
        if ($this->isExists($imageId)) {
            return $this->storage->url('arrivals/'.$imageId);
        }

        throw new ImageNotFoundException("Image not found- $imageId. Class - ".self::class);
    }

    public function deleteManyById(EloquentCollection $arrivalsFromDb): void
    {
        $arrivalsFromDb->each(function (NewArrival $arrival) {
            $this->delete($arrival->image_source);
        });
    }

    public function delete(string $imageId): bool
    {
        return $this->storage->delete('arrivals/'.$imageId);
    }

    public function isExists(string $fileName): bool
    {
        return $this->storage->exists('arrivals/'.$fileName);
    }
}
