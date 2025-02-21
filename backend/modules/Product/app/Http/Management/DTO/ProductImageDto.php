<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\DTO;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

final readonly class ProductImageDto
{
    public function __construct(
        /**@var <int, UploadedFile[]> $mainImages */
        public array $mainImages,
        public ?UploadedFile $previewImage
    ) {
    }

    public static function fromRequest(Request $request): ProductImageDto
    {
        return new self(
            mainImages: $request->file('images') ?? [],
            previewImage: $request->file('preview_image')
        );
    }
}