<?php

declare(strict_types=1);

namespace Modules\Brand\Services;

use Illuminate\Http\UploadedFile;
use Modules\Brand\Interfaces\BrandLogoStorageInterface;
use Modules\Brand\Models\Brand;

class BrandImageService
{
    public function __construct(
        protected BrandLogoStorageInterface $storage,
    ) {}

    public function upload(UploadedFile $image, Brand $brand): void
    {
        if ($brand->logo_source !== null) {
            $this->storage->delete($brand->logo_source);
        }

        $newLogoUrl = $this->storage->save($image);

        $brand->saveLogo($newLogoUrl, $image->hashName());
    }
}
