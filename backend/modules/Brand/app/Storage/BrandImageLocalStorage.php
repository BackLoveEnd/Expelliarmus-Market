<?php

declare(strict_types=1);

namespace Modules\Brand\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Modules\Brand\Http\Exceptions\FailedToUploadBrandLogoException;
use Modules\Brand\Interfaces\BrandLogoStorageInterface;
use Throwable;

class BrandImageLocalStorage implements BrandLogoStorageInterface
{
    public function __construct(
        private Filesystem $filesystem,
    ) {}

    public function get(?string $imageId): string
    {
        if ($imageId === null) {
            return $this->filesystem->url(config('brand.default_logo'));
        }

        if ($this->filesystem->exists($imageId)) {
            return $this->filesystem->url($imageId);
        }

        return $this->filesystem->url(config('brand.default_logo'));
    }

    public function save(UploadedFile $file): string
    {
        try {
            $this->filesystem->putFileAs('', $file, $file->hashName());

            return $this->get($file->hashName());
        } catch (Throwable $e) {
            throw new FailedToUploadBrandLogoException($e->getMessage());
        }
    }

    public function delete(?string $imageId): bool
    {
        return $this->filesystem->delete($imageId);
    }
}
