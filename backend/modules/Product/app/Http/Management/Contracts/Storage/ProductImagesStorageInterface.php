<?php

namespace Modules\Product\Http\Management\Contracts\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Modules\Product\Models\Product;

interface ProductImagesStorageInterface extends ImageManipulationInterface
{
    public function upload(UploadedFile $file, Product $product, ?string $hashName = null): string;

    /** @var Collection<int, UploadedFile> $files */
    public function uploadMany(Collection $files, Product $product): array;

    public function getOne(Product $product, ?string $imageId): string;

    public function getAll(Product $product): array;

    public function getAllFromSources(Product $product, array $imagesSources): array;

    public function delete(Product $product, string $imageId): bool;

    public function deleteMany(Product $product, Collection $sources): void;

    public function isExists(Product $product, string $imageId): bool;
}
