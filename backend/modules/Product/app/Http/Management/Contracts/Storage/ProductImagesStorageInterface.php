<?php

namespace Modules\Product\Http\Management\Contracts\Storage;

use Illuminate\Http\UploadedFile;
use Modules\Product\Models\Product;

interface ProductImagesStorageInterface extends ImageManipulationInterface
{
    public function upload(UploadedFile $file, Product $product, ?string $hashName = null): string;

    /**@var array<int, UploadedFile> $files */
    public function uploadMany(array $files, Product $product): array;

    public function getOne(Product $product, string $imageId): string;

    public function getAll(Product $product): array;

    public function getAllFromSources(Product $product, array $imagesSources): array;

    public function delete(Product $product, string $imageId): bool;

    public function isExists(Product $product, string $imageId): bool;
}