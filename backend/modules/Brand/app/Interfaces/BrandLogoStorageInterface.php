<?php

namespace Modules\Brand\Interfaces;

use Illuminate\Http\UploadedFile;

interface BrandLogoStorageInterface
{
    public function get(?string $imageId): string;

    public function save(UploadedFile $file): string;

    public function delete(?string $imageId): bool;
}
