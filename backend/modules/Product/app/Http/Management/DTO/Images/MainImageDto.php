<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\DTO\Images;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

final readonly class MainImageDto
{
    public function __construct(
        public int $order,
        public ?string $id = null,
        public ?UploadedFile $image = null,
        public ?string $existImageUrl = null,
    ) {}

    public static function collection(array $files): Collection
    {
        return collect($files)->map(function (array $files) {
            return new self(
                order: (int) $files['order'],
                id: $files['id'] ?? null,
                image: $files['image'] ?? null,
                existImageUrl: $files['image_url'] ?? null,
            );
        });
    }
}
