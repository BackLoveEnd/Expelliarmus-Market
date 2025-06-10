<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\DTO\Images;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

final readonly class ProductImageDto
{
    public function __construct(
        /** @var <int, UploadedFile[]> $mainImages */
        public Collection $mainImages,
        public ?UploadedFile $previewImage
    ) {}

    public static function fromRequest(Request $request): ProductImageDto
    {
        return new self(
            mainImages: $request->images ? MainImageDto::collection($request->images) : collect(),
            previewImage: $request->file('preview_image')
        );
    }
}
