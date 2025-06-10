<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Storage\Slider;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Modules\ContentManagement\Http\Dto\Slider\SliderContentDto;
use Modules\ContentManagement\Http\Exceptions\FailedToUploadContentException;
use Modules\ContentManagement\Models\ContentSlider;
use Throwable;

class SliderContentStorage
{
    protected Filesystem $storage;

    private array $uploadedImages = [];

    public function __construct(FilesystemManager $filesystemManager)
    {
        $this->storage = $filesystemManager->disk('public_content');
    }

    public function uploadMany(Collection $images): void
    {
        try {
            $images->each(function (SliderContentDto $dto) {
                $this->uploadedImages[] = $this->upload($dto->image);
            });
        } catch (Throwable $e) {
            foreach ($this->uploadedImages as $image) {
                $this->delete($image);
            }

            throw new FailedToUploadContentException($e->getMessage());
        }
    }

    public function deleteManyById(EloquentCollection $slides): void
    {
        $slides->each(function (ContentSlider $contentSlider) {
            $this->delete($contentSlider->image_source);
        });
    }

    public function upload(UploadedFile $file, ?string $hashName = null): false|string
    {
        $this->storage->makeDirectory('slider');

        $fileName = $hashName ?? $file->hashName();

        return $this->storage->putFileAs('slider', $file, $fileName);
    }

    public function delete(string $imageId): bool
    {
        return $this->storage->delete('slider/'.$imageId);
    }

    public function getImageUrl(string $imageId): ?string
    {
        if ($this->isExists($imageId)) {
            return $this->storage->url('slider/'.$imageId);
        }

        return $this->storage->url('slider/'.$this->defaultSlide());
    }

    public function defaultSlide(): string
    {
        return config('contentmanagement.default.slider.image');
    }

    public function isExists(string $fileName): bool
    {
        return $this->storage->exists('slider/'.$fileName);
    }
}
